<?php

namespace App\Modules\Users\Auth\Controllers\Api;

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Auth\Requests\AddDeviceTokenRequest;
use App\Modules\Users\Auth\Requests\Api\ResetPasswordRequest;
use App\Modules\Users\Auth\Requests\ApiRegisterRequest;
use App\Modules\Users\Auth\Requests\ChangePasswordRequest;
use App\Modules\Users\Auth\Requests\ForgetPasswordRequest;
use App\Modules\Users\Auth\Requests\LoginRequest;
use App\Modules\Users\Auth\Requests\VerificationRequest;
use App\Modules\Users\Models\UserDevice;
use App\Modules\Users\Resources\DeviceResorce;
use App\Modules\Users\Resources\UserConfigurationsResourceResorce;
use App\Modules\Users\Resources\UserResorce;
use App\Modules\Users\Models\User;
use App\Modules\Users\UseCases\ActivateUserUseCase\ActivateUserUseCaseInterface;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Modules\Users\UserEnums;
use Intervention\Image\Facades\Image;
use Twilio\Rest\Client;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomErrorException;
use App\Modules\Users\Events\UserModified;
use App\Modules\BaseApp\Api\BaseApiController;
use App\Modules\Users\Auth\Enum\TokenNameEnum;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Auth\Requests\Api\LogoutRequest;
use App\Modules\Users\Transformers\UserAuthTransformer;
use App\Modules\Users\Auth\Requests\Api\UserActivateOtp;
use App\Modules\Users\Auth\Requests\Api\UserLoginRequest;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Auth\Requests\Api\UserRegisterRequest;
use App\Modules\Users\Auth\Requests\Api\UserTypeDataRequest;
use App\Modules\Users\Auth\Requests\Api\UserBasicDataRequest;
use App\Modules\Users\Auth\Requests\Api\ChangeLanguageRequest;
use App\Modules\Users\Auth\TokenManager\TokenManagerInterface;
use App\Modules\Users\Auth\Requests\Api\UserLoginSocialRequest;
use App\Modules\Users\Auth\Requests\Api\StoreFirebaseTokenRequest;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\Modules\Users\UseCases\LoginSocialUseCase\LoginSocialUseCase;
use App\Modules\Users\UseCases\RegisterUseCase\RegisterUseCaseInterface;
use App\Modules\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\Modules\Users\Auth\Requests\Api\UserActivateOtpRequest;

class AuthApiController extends BaseApiController
{
    private $loginSocialUseCase;
    protected $firebaseRepository;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        LoginUseCaseInterface $loginUseCase,
        UserRepositoryInterface $userRepository,
//        ParserInterface $parserInterface,
        RegisterUseCaseInterface $registerUseCase,
//        SendActivationMailUseCaseInterface $sendActivationMailUseCase,
//        ActivateUserUseCaseInterface $activateUserUseCase,
//        LoginSocialUseCase $loginSocialUseCase,
//        FirebaseTokenRepositoryInterface $firebaseRepository,
//        TokenManagerInterface $tokenManager
    )
    {
        $this->loginUseCase = $loginUseCase;
        $this->registerUseCase = $registerUseCase;
//        $this->sendActivationMailUseCase = $sendActivationMailUseCase;
//        $this->activateUserUseCase = $activateUserUseCase;
        $this->repository = $userRepository;
//        $this->parserInterface = $parserInterface;
//        $this->loginSocialUseCase = $loginSocialUseCase;
//        $this->firebaseRepository = $firebaseRepository;
//        $this->tokenManager = $tokenManager;
    }

    public function login(LoginRequest $request){
        $data = $this->loginUseCase->login($request->all(), $this->repository);
        return $data;
    }

    public function getProfile(){
        $user = $this->loginUseCase->profile($this->repository);
        return $user;
    }

    public function refreshToken()
    {
        // TODO you have to change refresh token to be used passport
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                $newToken = JWTAuth::parseToken()->refresh();

                $meta = [
                    'token' => $newToken,
                ];
                //send data to transformer
                return $this->transformDataModInclude(JWTAuth::user(), '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            }
        } catch (TokenExpiredException $e) {
            $newToken = JWTAuth::parseToken()->refresh();
            $meta = [
                'token' => $newToken,
            ];
            //send data to transformer
            return $this->transformDataModInclude(JWTAuth::user(), '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
        }
    }

    private static function getRefererIdByReferCode($refer_code) : int
    {
        $referer = User::where('refer_code', $refer_code)->first();
        return $referer->id;
    }

    private static function handleProfileImage($profile_image, $directory) : string
    {
        $profile_image_url = "user-".time().".png";
        $path = public_path().$directory . $profile_image_url;
        Image::make(file_get_contents($profile_image))->save($path);
        return $directory.$profile_image_url;
    }

    private function getUniqueReferCode(){
        $code = Str::random(8);
        $user = User::where('refer_code', $code)->first();
        if (!isset($user)){
            return $code;
        }else{
            $this->getUniqueReferCode();
        }
    }

    public function register(ApiRegisterRequest $request)
    {
        try {
            if (isset($request->refer_code)){
                $referer_id = self::getRefererIdByReferCode($request->refer_code);
            }else{
                $referer_id = null;
            }
            if (isset($request->profile_image)){
                $photo = self::handleProfileImage($request->profile_image, '/uploads/users/');
            }else{
                $photo = null;
            }
            $requestData = [
                'full_name' => $request->full_name,
                'phone' => $request->phone_number,
                'email' => $request->email_address,
                'country_id' => $request->country_id,
                'photo' => $photo,
                'password' => Hash::make($request->password),
                'country_code' => $request->country_code,
                'referer_id' => $referer_id,
                'refer_code' => $this->getUniqueReferCode()

            ];
            $user = $this->registerUseCase->register($requestData, $this->repository);


            if (!is_null($user)) {

                return customResponse([
                    'user' => new UserResorce($user),
                    'token'=> $user->createToken('AccessToken')->accessToken
                ], __("Account created successfully"), 200, StatusCodesEnum::DONE);
            }
        } catch (\Throwable $e) {
            Log::error($e);
//            throw  $e;
            throw new CustomErrorException($e->getMessage());

        }
    }

    public function editProfile(Request $request){
        $user = auth('api')->user();
        if (isset($request->profile_image)){
            $user->photo = self::handleProfileImage($request->profile_image, '/uploads/users/');
        }
        if (isset($request->full_name)){
            $user->full_name = $request->full_name;
        }
        if (isset($request->email)){
            $checkEmail = User::where('email', $request->email)
                ->where('id', '<>', $user->id)
                ->first();
            if (!isset($checkEmail)){
                $user->email = $request->email;
            }else{
                return customResponse((object)[], trans('api.Email already taken.'),422, StatusCodesEnum::FAILED);
            }
        }
        $user->save();
        return customResponse(new UserResorce($user), trans('api.Profile updated successfully'), 200, StatusCodesEnum::DONE);
    }

    public function deleteDevices(){
        $user = User::where('phone', \request()->phone_number)->first();
        if (isset($user)){
            $user->devices()->delete();
            return customResponse((object)[], trans('api.Devices deleted successfully'), 200, StatusCodesEnum::DONE);
        }
        return customResponse((object)[], trans('api.User does not exist.'),422, StatusCodesEnum::FAILED);
    }

    public function deleteMyDevice(Request $request){
        $user = auth('api')->user();
        $device_id = $request->device_id;
        $device = $user->devices()
            ->where(function ($query) use ($request, $device_id){
                $query->where('device_id', $device_id)
                    ->orWhere('id', $device_id);
            })
            ->where('user_id', $user->id)
            ->first();
        if (isset($device)){
            if ($device->device_id != $request->header('device-id')){
                $device->delete();
                return customResponse((object)[], trans('api.Device deleted successfully'), 200, StatusCodesEnum::DONE);
            }else{
                return customResponse((object)[], trans('api.Can not delete your current device'), 422, StatusCodesEnum::FAILED);
            }
        }
        return customResponse((object)[], trans('api.Device not found'), 422, StatusCodesEnum::FAILED);
    }

    public function verifyPhone(VerificationRequest $request)
    {
        $user = User::where('phone', $request->phone_number)->first();
        $user->is_verified = 1;
        $user->save();
        /* Save user device */
        if (isset($request->device_name)){
            $device = new UserDevice;
            $device->device_name = $request->device_name;
            $device->device_type = request()->header('device-type');
            $device->device_id = request()->header('device-id');
            $device->user_id = $user->id;
            $device->is_tablet = $request->is_tablet;
            $device->save();
        }
        /* Authenticate user */
        return customResponse((object)[], 'Phone number verified successfully',200, StatusCodesEnum::DONE);
        try{
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
            $verification = $twilio->verify->v2->services($twilio_verify_sid)
                ->verificationChecks
                ->create([
                    'to' => $request->country_code.$request->phone_number,
                    'code' => $request->verification_code
                ]);
            if ($verification->valid) {
                $user = tap(User::where('phone', $request->phone_number))->update(['is_verified' => 1]);
                /* Save user device */
                if (isset($request->device_name)){
                    $device = new UserDevice;
                    $device->user_id = $user->id;
                    $device->device_type = request()->header('device-type');
                    $device->device_id = request()->header('device-id');
                    $device->device_name = $request->device_name;
                    $device->is_tablet = $request->is_tablet;
                    $device->save();
                }
                /* Authenticate user */
                return customResponse((object)[], 'Phone number verified successfully',200, StatusCodesEnum::DONE);
            }
            return customResponse((object)[], 'verification failed',422, StatusCodesEnum::FAILED);
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }
    }

    public function getUserConfig(){
        $user = auth('api')->user();
        return customResponse(new UserConfigurationsResourceResorce($user), trans("api.Done"), 200, StatusCodesEnum::DONE);
    }

    public function allowPushNotifications(Request $request){
        $user = auth('api')->user();
        $allow_notifications = $request->allow_notifications;
        if ($allow_notifications == true)
            $allow_notifications = 1;
        else
            $allow_notifications = 0;
        $device = $user->devices()->where('device_id', $request->header('device-id'))->first();
        $device->allow_push_notifications = $allow_notifications;
        $device->save();
        return customResponse(new UserConfigurationsResourceResorce($user), trans("api.Done"), 200, StatusCodesEnum::DONE);
    }

    public function getactivateOtp(UserActivateOtpRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $code = $data->otp;

            $user = $this->activateUserUseCase->activateWithOtp($code, $this->repository);
            if (!$user) {
               return formatErrorValidation([
                'status' => 422,
                'title' => 'invalid code',
                'detail' => trans('auth.invalid code')
               ]);
            }

        return response()->json([
            'message' => trans('api.Account activated')
        ]);

    }

    public function forgetPassword(ForgetPasswordRequest $request){
        try {
//            $this->sendVerifyMessage($request->country_code.$request->phone_number);
            return customResponse((object)[], __("Verification code sent successfully"),200, StatusCodesEnum::DONE);
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }
    }

    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients string or array of phone number of recepient
     */
    private function sendVerifyMessage($phone)
    {
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($phone, "sms");
    }

    public function changePassword(ChangePasswordRequest $request){
        try{
            $user = auth('api')->user();
            $password_check = Hash::check($request->old_password, $user->password);
            if ($password_check){
                $user->password = Hash::make($request->new_password);
                $user->save();
                return customResponse((object)[], __("Password changed successfully"),200, StatusCodesEnum::DONE);
            }else{
                return customResponse((object)[], __("Password didn't match"),422, StatusCodesEnum::FAILED);
            }
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }
    }

    public function addDeviceToken(AddDeviceTokenRequest $request){
        try{
            $user_id = auth('api')->user()->id;
            $device = UserDevice::where(['user_id' => $user_id, 'is_tablet' => $request->is_tablet])->first();
            if (isset($device)){
                $device->device_token = $request->device_token;
                $device->save();
            }
            return customResponse((object)[], __("Device Token Added Successfully"),200, StatusCodesEnum::DONE);
        }catch (\Exception $e){
            return customResponse((object)[], $e->getMessage(),422, StatusCodesEnum::FAILED);
        }
    }

    public function logout(){
        $user = auth('api')->user();
        $user->devices()->where('is_tablet', request()->is_tablet)->delete();
        $user = Auth::user()->token();
        $user->revoke();
        return customResponse((object)[], __("Logged Out Successfully"),200, StatusCodesEnum::DONE);
    }

    public function resetPassword(ResetPasswordRequest $request){
        $user = User::where(['phone' => $request->phone_number, 'country_code' => $request->country_code])
            ->first();
        if (isset($user)){
            $user->password = Hash::make($request->password);
            $user->save();
            return customResponse((object)[], __("Password reset successfully"), 200, StatusCodesEnum::DONE);
        }
        return customResponse((object)[], __("User not found"), 422, StatusCodesEnum::FAILED);
    }

    public function activateOtp(UserActivateOtp $userActivateOtp)
    {
        $data = $userActivateOtp->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $user = $this->repository->findUserByOtpTokenAndConfirmToken($data->otp,$data->confirm_token);

            if (!is_null($user)) {
                $meta = [
                    'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $user),
                ];
                $user->update([
                    'confirm_token' => null,
                    'otp' => null,
                              ]);
                //send data to transformer
                return $this->transformDataModInclude($user, '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            } else {
                throw new Exception(trans('app.invalid_token'));
            }
        } catch (\Exception $exception) {
            $errorArray = [
                'status' => 422,
                'title' => $exception->getMessage(),
                'detail' => $exception->getMessage(),
            ];
            return formatErrorValidation($errorArray);
        }
    }

    // login and register by social media
    public function loginAndRegisterUsingFacebook(UserLoginSocialRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $requestData = [
            'token' => $data->token,
            'secret_id' => $data->secret_id,
            'user_type' => $data->user_type ?? env('FALLBACK_SOCIAL_TYPE'),
            'providerName' => 'facebook'
        ];
        try {
            $useCase = $this->loginSocialUseCase->loginOrRegisterByFacebook($requestData);
            if (!is_null($useCase['user'])) {
                $this->tokenManager->revokeUserAccessTokenBy(['name' => TokenNameEnum::API_Token], $useCase['user']);

                $meta = [
                    'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $useCase['user']),
                    'message' => trans('api.Successfully Logged In'),
                ];

                $include = '';
                if ($useCase['user']->type == UserEnums::STUDENT_TYPE) {
                    $include = 'student';
                }

                if ($useCase['user']->type == UserEnums::PARENT_TYPE) {
                    $include = 'parent';
                }
                //send data to transformer
                return $this->transformDataModInclude($useCase['user'], $include, new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            } else {
                $errorArray = [
                    'status' => 422,
                    'title' => $useCase['message'],
                    'detail' => $useCase['detail'],
                ];
                return formatErrorValidation($errorArray);
            }
        } catch (\Exception $exception) {
            if (Str::contains($exception->getMessage(), '400 Bad Request')) {
                $errorArray = [
                    'status' => 500,
                    'title' => 'invalid_token',
                    'detail' => trans('auth.Maybe the token expired or malformed'),
                ];
                return formatErrorValidation($errorArray, 500);
            } else {
                $errorArray = [
                    'status' => 500,
                    'title' => snake_case($exception->getMessage()),
                    'detail' => $exception->getMessage(),
                ];
                return formatErrorValidation($errorArray, 500);
            }
        }
    }

//    public function logout(LogoutRequest $request)
//    {
//        $user = Auth::guard('api')->user();
//        $data = $request->getContent();
//        $data = $this->parserInterface->deserialize($data);
//        $data = $data->getData();
//
//        try {
//            $this->firebaseRepository->delete($user, $data->toArray());
//
//            $this->tokenManager->revokeAuthAccessToken();
//
//            return response()->json(
//                [
//                    "meta" => [
//                        'message' => trans('api.Successfully Logged Out')
//                    ]
//                ]
//            );
//        } catch (\Exception $e) {
//            $errorArray = [
//                'status' => $e->getCode(),
//                'title' => $e->getMessage(),
//                'detail' => $e->getTrace()
//            ];
//            return formatErrorValidation($errorArray, 500);
//        }
//    }

    public function deleteMyAccount(){
        $user = Auth::user();
        $user->delete();
        return customResponse((object)[], __("Account Deleted Successfully"),200,StatusCodesEnum::DONE);
    }

    public function myDevices(){
        $devices = auth('api')->user()->devices;
        return customResponse(DeviceResorce::collection($devices), "Done", 200, StatusCodesEnum::DONE);
    }

    public function changeLanguage(ChangeLanguageRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $user = auth()->user();

        try {
            $updateData['language'] = $data->lang_slug;
            $update = $this->repository->update($user, $updateData);
            if ($update) {
                $meta = [
                    'message' => trans('auth.Language changed successfully')
                ];
                return response()->json(['meta' => $meta], 200);
            }
        } catch (\Throwable $e) {
            throw new CustomErrorException($e->getMessage());
        }
    }

    public function storeFCMToken(StoreFirebaseTokenRequest $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $token = $this->firebaseRepository->store($user, $data->toArray());

        $meta = [
            'message' => trans('auth.Token stored successfully')
        ];

        return response()->json(['meta' => $meta], 200);
    }

    public function validateBasicData(UserBasicDataRequest $request)
    {

        return response()->json([]);
    }

    public function validateTypeData(UserTypeDataRequest $request)
    {

        return response()->json([]);
    }
}
