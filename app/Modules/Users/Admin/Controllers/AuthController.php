<?php


namespace App\Modules\Users\Admin\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Admin\Models\Admin;
use App\Modules\Users\Admin\Resources\AdminResource;
use App\Modules\Users\UserEnums;
use App\Modules\BaseApp\Controllers\AjaxController;
use App\Modules\Users\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends AjaxController
{
    public function login(Request $request){
        $v = Validator::make($request->all(), [
            'email' => 'required|exists:admins,email',
            'password' => 'required'
        ]);
        if ($v->fails()){
            return customResponse((object)[], $v->errors()->first(), 422, StatusCodesEnum::FAILED);
        }

        $admin = Admin::where('email', $request->email)->first();
        $password_check = Hash::check($request->password, $admin->password);
        if (!$password_check){
            return customResponse((object)[], "invalid password", 422, StatusCodesEnum::FAILED);
        }
        return customResponse([
            "user" => new AdminResource($admin),
            "token" => $admin->createToken('AdminAccessToken')->accessToken,
        ], "Logged in successfully", 200, StatusCodesEnum::DONE);
    }

    public function logout()
    {
        $token = auth('admin')->user()->token();
        $token->revoke();
        return customResponse((object)[], __("Logged Out Successfully"),200, StatusCodesEnum::DONE);
    }
}
