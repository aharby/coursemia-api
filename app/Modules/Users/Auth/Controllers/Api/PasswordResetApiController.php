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
use App\Modules\Users\User;

class PasswordResetApiController extends Controller
{
    /**
     * Handle sending the reset link email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
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
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET?
         customResponse([], __('auth.Passoword was reset successfully'), 200, StatusCodesEnum::DONE):
         customResponse([],__('auth.Passowrd couldn\'t be reset'), 422, StatusCodesEnum::FAILED );
    }
}
