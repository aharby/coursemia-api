<?php

namespace App\Modules\Users\Auth\Controllers\Api;

use App\Enums\StatusCodesEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Modules\Users\Models\User;
use App\Rules\ValidFullPhoneNumber;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;

class PasswordResetApiController extends Controller
{
    /**
     * Handle sending the reset link email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
 
        $user = User::where('email', $request['email'])->first();

        if(!$user->hasVerifiedEmail())
            return customResponse([], __('auth.User not verified'), 422, StatusCodesEnum::EMAIL_NOT_VERIFIED);

        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT?
         customResponse([], __('auth.Password reset link sent successfully'), 200, StatusCodesEnum::DONE):
         customResponse([],__('auth.Password reset link sent failed'), 422, StatusCodesEnum::FAILED );
    }

    /**
     * Handle resetting the password.
     */
    public function confirmResetUsingMail(Request $request)
    {        
        $validator = Validator::make(
            $request->all() ,[
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required',
            'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{9,}$/',
            'confirmed']    
        ], ['password.regex' => __('auth.Password Regex')]);

        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET?
         customResponse([], __('auth.Password was reset successfully'), 200, StatusCodesEnum::DONE):
         customResponse([],__('auth.Passowrd couldn\'t be reset'), 422, StatusCodesEnum::FAILED );
    }

    public function sendResetPhoneCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone',
            'country_code' => ['required', 'exists:countries,country_code']
        ]);

        $phone = $request['phone_number'];
        $country_code = $request['country_code'];

        $is_verified = User::where('phone', $phone)
            ->where('is_verified', true)
            ->exists();

        if(!$is_verified)
            return customResponse([], __('auth.User phone not verified'), 422, StatusCodesEnum::PHONE_NUMBER_NOT_VERIFIED);

        try 
        {
            // send verify message
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
            $twilio->verify->v2->services($twilio_verify_sid)
                ->verifications
                ->create($country_code.$phone, "sms");
            return customResponse((object)[], __("auth.Password reset code sent successfully"),200, StatusCodesEnum::DONE);
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }

    }

    public function confirmResetUsingPhone(Request $request)
    {
        $validator = Validator::make(
            $request->all() ,[
            'phone_number' => 'required|exists:users,phone',
            'country_code'      => 'required|exists:countries,country_code',
            'verification_code' => 'required',
            'password' => ['required','min:9',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{9,}$/',
                'confirmed']    
        ], ['password.regex' => __('auth.Password Regex')]);

        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $phone = $request['phone_number'];
        $country_code = $request['country_code'];

        try{
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
            $verification = $twilio->verify->v2->services($twilio_verify_sid)
                ->verificationChecks
                ->create([
                    'to' => $country_code . $phone,
                    'code' => $request->verification_code
                ]);
                
            if ($verification->valid) {
                $user = User::where('phone', $phone)
                            ->where('country_code', $country_code)->first();
                if (isset($user)){
                    $user->password = Hash::make($request->password);
                    $user->save();
                    return customResponse((object)[], __("auth.Password was reset successfully"), 200, StatusCodesEnum::DONE);
                }
                return customResponse((object)[], __("auth.User not found"), 422, StatusCodesEnum::FAILED);
            }
            return customResponse((object)[], 'auth.verification failed',422, StatusCodesEnum::FAILED);
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }
    }
}
