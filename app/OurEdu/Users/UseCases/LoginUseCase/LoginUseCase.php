<?php

declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\LoginUseCase;

use App\OurEdu\Users\Auth\Enum\DeviceEnum;
use App\OurEdu\Users\Auth\Enum\LoginEnum;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\SendLoginOtp\SendLoginOtp;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginUseCase implements LoginUseCaseInterface
{

    private SendLoginOtp $sendLoginOtp;

    public function __construct(SendLoginOtp $sendLoginOtp)
    {
        $this->sendLoginOtp = $sendLoginOtp;
    }

    public function login(array $request, UserRepositoryInterface $userRepository, string $attribute = 'email'): array
    {
        $user = null;
        if ($attribute == "email") {
            $user = $userRepository->findByEmail($request['email'], $request['abilities_user']);
        }

        if ($attribute == "mobile") {
            $user = $userRepository->findByPhone($request['email'], $request['abilities_user']);
        }

        $validateLogin = $this->validateLogin($request, $user);

        if ($validateLogin) {
            return $validateLogin;
        }

        $login = [$attribute => $request['email'], 'password' => $request['password'], 'username' => $user->username];
        if (Auth::attempt($login, $request['remember_me'])) {
            if ($user->type == UserEnums::STUDENT_TYPE) {
                if (is_null($user->student->classroom_id) && request()->has('otp')) {
                    $this->sendLoginOtp->send($user);
                    $confirmToken = Str::random(64);
                    $user->update([
                        'confirm_token' => $confirmToken
                    ]);
                    $loginCase['status'] = $confirmToken;
                    $loginCase['message'] = 'please use token';
                    $loginCase['detail'] = trans('app.please use otp');
                    $loginCase['user'] = null;
                    return $loginCase;
                }
            }

            $loginCase['user'] = $user;
            $loginCase['user']['back_ground_slug'] = isset($user->student->gradeClass->gradeColor) ?
                $user->student->gradeClass->gradeColor->slug : '';

            $loginCase['message'] = 'Welcome to your dashboard';
            $loginCase['detail'] = trans('auth.Welcome to your dashboard');
            return $loginCase;
        } else {
            $loginCase['user'] = null;
            $loginCase['message'] = 'Oopps Something is broken';
            $loginCase['detail'] = trans('app.Oopps Something is broken');
            return $loginCase;
        }
    }

    public function loginWithUsername(array $request, UserRepositoryInterface $userRepository): array
    {
        $loginCase = [];
        if(isset($request['abilities_user']) && $request['abilities_user']){
            $loginCase['user'] = null;
            $loginCase['message'] = 'There is no account with this email';
            $loginCase['detail'] = trans('auth.There is no account with this email');
            return $loginCase;
        }
        $user = $userRepository->findByUsername($request['email']);
        if (!$user) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'There is no account with this email';
            $loginCase['detail'] = trans('auth.There is no account with this email');
            return $loginCase;
        }
        if (is_null($user->password) && is_null($user->facebook_id) && is_null($user->twitter_id)) {
            $loginCase['user'] = $user;
            $loginCase['shouldChangePassword'] = true;
            $loginCase['message'] = 'Welcome to your dashboard';
            $loginCase['detail'] = trans('auth.Welcome to your dashboard');
            return $loginCase;
        }

        if (!$user) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'There is no account with this email';
            $loginCase['detail'] = trans('auth.There is no account with this email');
            return $loginCase;
        }

        if (isset($request['device_type'])) {
            $validLogin = $this->validateDeviceWithType($request['device_type'], $user->type);
            if (!$validLogin) {
                $loginCase['user'] = null;
                $loginCase['message'] = 'Cannot Login From This Device';
                $loginCase['detail'] = trans('auth.Cannot Login From This Device');
                return $loginCase;
            }
        }

        if (!Hash::check(trim($request['password'] ?? ''), $user->password)) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'Trying to login with invalid password';
            $loginCase['detail'] = trans('auth.Trying to login with invalid password');
            return $loginCase;
        }
        if (!$user->is_active) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is banned';
            $loginCase['detail'] = trans('auth.This account is banned');
            return $loginCase;
        }
        if (!is_null($user->suspended_at)) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is suspended';
            $loginCase['detail'] = trans('auth.This account is suspended');
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
            $loginCase['message'] = 'Welcome to your dashboard';
            $loginCase['detail'] = trans('auth.Welcome to your dashboard');
            return $loginCase;
        } else {
            $loginCase['user'] = null;
            $loginCase['message'] = 'Oopps Something is broken';
            $loginCase['detail'] = trans('app.Oopps Something is broken');
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
            $loginCase['message'] = 'There is no account with this email';
            $loginCase['detail'] = trans('auth.There is no account with this email');

            return $loginCase;
        }

        if (isset($request['user_type']) && $request['user_type'] == UserEnums::ADMIN_TYPE) {
            if (!$user->super_admin) {
                $loginCase['user'] = null;
                $loginCase['message'] = 'Trying to login with non super admin account';
                $loginCase['detail'] = trans('auth.Trying to login with non super admin account');

                return $loginCase;
            }
        }

        if (isset($request['device_type'])) {
            $validLogin = $this->validateDeviceWithType($request['device_type'], $user->type);
            if (!$validLogin) {
                $loginCase['user'] = null;
                $loginCase['message'] = 'Cannot Login From This Device';
                $loginCase['detail'] = trans('auth.Cannot Login From This Device');

                return $loginCase;
            }
        }

        if (!Hash::check(trim($request['password']), $user->password)) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'Trying to login with invalid password';
            $loginCase['detail'] = trans('auth.Trying to login with invalid password');

            return $loginCase;
        }
        if (!$user->is_active) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is banned';
            $loginCase['detail'] = trans('auth.This account is banned');

            return $loginCase;
        }
        if (!is_null($user->suspended_at)) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is suspended';
            $loginCase['detail'] = trans('auth.This account is suspended');

            return $loginCase;
        }
        if ($user->deleted_ios_action) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is suspended';
            $loginCase['detail'] = trans('auth.This account is suspended');

            return $loginCase;
        }


        if (!$user->confirmed && ($user->type == UserEnums::STUDENT_TYPE ||$user->type == UserEnums::PARENT_TYPE)) {
            $loginCase['user'] = null;
            $loginCase['message'] = 'This account is not confirmed';
            $loginCase['detail'] = trans('auth.This account is not confirmed');

            return $loginCase;
        }

        return $loginCase;
    }
}
