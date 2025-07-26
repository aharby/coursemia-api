<?php

declare(strict_types=1);

namespace App\Modules\Users\UseCases\LoginUseCase;

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Auth\Enum\DeviceEnum;
use App\Modules\Users\Auth\Enum\LoginEnum;
use App\Modules\Users\Models\UserDevice;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Resources\UserResorce;
use App\Modules\Users\UseCases\SendLoginOtp\SendLoginOtp;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\GuestDevice;


class LoginUseCase implements LoginUseCaseInterface
{
    public function login(array $request, UserRepositoryInterface $userRepository): array
    {
        $user = null;
        $is_verified = false;
        
        if (array_key_exists('email', $request)) {
            $user = $userRepository->findByEmail($request['email']);
        }
        else{
            $user = $userRepository->findByPhone($request['phone_number'], $request['country_code']);
        }

        $loginCase = array();
        $loginCase['data'] = (object)[];
        $loginCase['message'] = __('auth.Invalid login details');
        $loginCase['status_code'] = StatusCodesEnum::FAILED;

        if(!isset($user)){
            return $loginCase;
        }

        if(!($user->is_verified && $user->hasVerifiedEmail())){
            $loginCase['message'] = __('auth.User not verified');
            $loginCase['status_code'] = StatusCodesEnum::PHONE_NUMBER_AND_EMAIL_NOT_VERIFIED;
            return $loginCase;
        }

        if(!$user->is_verified){
            $loginCase['message'] = __('auth.User not verified');
            $loginCase['status_code'] = StatusCodesEnum::PHONE_NUMBER_NOT_VERIFIED;
            return $loginCase;
        }
        
        if(!$user->hasVerifiedEmail()){
            $loginCase['message'] = __('auth.User not verified');
            $loginCase['status_code'] = StatusCodesEnum::EMAIL_NOT_VERIFIED;
            return $loginCase;
        }

        $password_check = Hash::check($request['password'], $user->password);

        if(!$password_check){
            return $loginCase;
        }

        $devices = UserDevice::where('user_id', $user->id)->get(); 

        $devices_count = $devices->count();
        $device_exists = $devices->firstWhere('device_id', request()->header('device-id')) !== null;
        $first_device = $devices->first(); 

        if ((!$device_exists && $devices_count >= 2)
            || (!$device_exists && $first_device && $first_device->is_tablet == $request['is_tablet'])) {
            $loginCase['message'] = __('auth.Maximum device numbers exceeded');
            return $loginCase;
        }

        if(!$device_exists && 
            ( !$first_device  || $first_device->is_tablet != $request['is_tablet'])){
            // save user device
            $user_device = new UserDevice;
            $user_device->user_id = $user->id;
            $user_device->device_type = request()->header('device-type');
            $user_device->device_id = request()->header('device-id');
            $user_device->is_tablet = $request['is_tablet'];
            $user_device->device_name = $request['device_name'];
            $user_device->save();

            //sync guest data
            $guestDevice = GuestDevice::where('guest_device_id', request()->header('device-id'))
                        ->first();
                        
            if(isset($guestDevice)){
                $cartCourses = $guestDevice->cartCourses->pluck('course');

                foreach ($guestDevice->cartCourses as $cartCourse) {
                    $cartCourse->guest_device_id = null;
                    $cartCourse->user_id = $user->id;
                    $cartCourse->save();
                }
                $guestDevice->delete();

            }

        }
            
        $loginCase['data'] = [];
        $loginCase['data']['user'] = new UserResorce($user);
        $loginCase['data']['token'] = $user->createToken('AccessToken')->accessToken;
        $loginCase['message'] = __('auth.Logged in successfully');
        $loginCase['status_code'] = StatusCodesEnum::DONE;

        return $loginCase;
    }

    public function profile(UserRepositoryInterface $userRepository) : array
    {
        $user = auth('api')->user();
        $loginCase['data'] = new UserResorce($user);
        $loginCase['message'] = __('auth.Logged in successfully');
        $loginCase['status_code'] = StatusCodesEnum::DONE;
        return $loginCase;
    }

    public function loginWithUsername(array $request, UserRepositoryInterface $userRepository): array
    {
        $loginCase = [];
        if(isset($request['abilities_user']) && $request['abilities_user']){
            $loginCase['user'] = null;
            $loginCase['message'] = __('auth.There is no account with this email');
            return $loginCase;
        }
        $user = $userRepository->findByUsername($request['email']);
        if (!$user) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'auth.There is no account with this email';
            return $loginCase;
        }
        if (is_null($user->password) && is_null($user->facebook_id) && is_null($user->twitter_id)) {
            $loginCase['user'] = $user;
            $loginCase['shouldChangePassword'] = true;
            $loginCase['message'] = __('auth.Welcome to your dashboard');
            return $loginCase;
        }

        if (!$user) {
            $loginCase['user'] = null;
            $loginCase['message'] = __('auth.There is no account with this email');
            return $loginCase;
        }

        if (isset($request['device_type'])) {
            $validLogin = $this->validateDeviceWithType($request['device_type'], $user->type);
            if (!$validLogin) {
                $loginCase['user'] = null;
                $loginCase['message'] = __('auth.Cannot Login From This Device');
                return $loginCase;
            }
        }

        if (!Hash::check(trim($request['password'] ?? ''), $user->password)) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.Trying to login with invalid password');
            return $loginCase;
        }
        if (!$user->is_active) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is banned');
            return $loginCase;
        }
        if (!is_null($user->suspended_at)) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is suspended');
            return $loginCase;
        }

        //@ToDo: we should run this after activation dynamic links
//        if (!$user->confirmed) {
//            $loginCase['user'] = null;
//            $loginCase['message'] = 'This account is not confirmed';
//            $loginCase['detail'] = trans('auth.This account is not confirmed');
//            return $loginCase;
//        }
        $login = ['username' => $request['email'], 'password' => $request['password'] ?? ''];
        if (Auth::attempt($login, $request['remember_me'])) {
            $loginCase['user'] = $user;
            if ($user->type == 'student') {
                $loginCase['user']['back_ground_slug'] = isset($user->student->gradeClass->gradeColor) ?
                    $user->student->gradeClass->gradeColor->slug : '';

            }
            $loginCase['message'] = trans('auth.Welcome to your dashboard');
            return $loginCase;
        } else {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.Oopps Something is broken');
            return $loginCase;
        }
    }

    public function validateDeviceWithType($deviceType, $userType)
    {
        $validLogin = true;
        switch ($deviceType) {
            case DeviceEnum::MOBILE_DEVICE_TYPE:
                if (!in_array($userType, LoginEnum::getMobileLoginTypes())) {
                    $validLogin = false;
                }
                break;
            case DeviceEnum::WEB_CONTENT_AUTHOR:
                if (!in_array($userType, LoginEnum::getWebContentAuthorLoginTypes())) {
                    $validLogin = false;
                }
                break;
            case DeviceEnum::WEB_SME:
                if (!in_array($userType, LoginEnum::getWebSmeLoginTypes())) {
                    $validLogin = false;
                }
                break;
            case DeviceEnum::WEB_STUDENT:
                if (!in_array($userType, LoginEnum::getWebStudentLoginTypes())) {
                    $validLogin = false;
                }
                break;
            case DeviceEnum::ASSESSMENT_APP:
                if (!in_array($userType, LoginEnum::getAssessmentAppLoginTypes())) {
                    $validLogin = false;
                }
                break;
        }
        return $validLogin;
    }

    private function validateLogin(array $request, $user): array
    {
        $loginCase = [];
        if (!$user) {
            $loginCase['user'] = null;
            $loginCase['message'] = __('There is no account with this email');

            return $loginCase;
        }

        if (isset($request['user_type']) && $request['user_type'] == UserEnums::ADMIN_TYPE) {
            if (!$user->super_admin) {
                $loginCase['user'] = null;
                $loginCase['message'] = __('auth.Trying to login with non super admin account');

                return $loginCase;
            }
        }

        if (isset($request['device_type'])) {
            $validLogin = $this->validateDeviceWithType($request['device_type'], $user->type);
            if (!$validLogin) {
                $loginCase['user'] = null;
                $loginCase['message'] = trans('auth.Cannot Login From This Device');

                return $loginCase;
            }
        }

        if (!Hash::check(trim($request['password']), $user->password)) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.Trying to login with invalid password');

            return $loginCase;
        }
        if (!$user->is_active) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is banned');

            return $loginCase;
        }
        if (!is_null($user->suspended_at)) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is suspended');

            return $loginCase;
        }
        if ($user->deleted_ios_action) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is suspended');

            return $loginCase;
        }


        if (!$user->confirmed && ($user->type == UserEnums::STUDENT_TYPE ||$user->type == UserEnums::PARENT_TYPE)) {
            $loginCase['user'] = null;
            $loginCase['message'] = trans('auth.This account is not confirmed');

            return $loginCase;
        }

        return $loginCase;
    }
}
