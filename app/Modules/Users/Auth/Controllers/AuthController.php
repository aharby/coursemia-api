<?php

namespace App\Modules\Users\Auth\Controllers;

use App\Modules\BaseApp\Controllers\BaseController;
use App\Modules\Users\Auth\Requests\ResetPasswordRequest;
use App\Modules\Users\Auth\Requests\UpdatePasswordRequest;
use App\Modules\Users\Auth\Requests\UserLoginRequest;
use App\Modules\Users\Events\UserModified;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\UseCases\ActivateUserUserCase\ActivateUserUseCaseInterface;
use App\Modules\Users\UseCases\ForgetPasswordUseCase\ForgetPasswordUseCaseInterface;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\Modules\Users\UserEnums;
use Dompdf\Exception;

use function Aws\boolean_value;

class AuthController extends BaseController
{
    public function __construct(LoginUseCaseInterface $loginUseCase, ForgetPasswordUseCaseInterface $forgetPasswordUseCase, UserRepositoryInterface $userRepository, ActivateUserUseCaseInterface $activateUserUseCase)
    {
        $this->middleware('guest');
        $this->module = 'auth';
        $this->loginUseCase = $loginUseCase;
        $this->forgetPasswordUseCase = $forgetPasswordUseCase;
        $this->repository = $userRepository;
        $this->activateUserUseCase = $activateUserUseCase;

    }

    public function getLogin()
    {
        $data['page_title'] = trans('auth.Login');
        $data['module'] = $this->module;
        return view($this->module . '.login', $data);
    }

    public function getLoginSchoolAccount()
    {
        $data['page_title'] = trans('auth.Login');
        $data['module'] = $this->module;
        return view($this->module . '.school-login', $data);
    }

    public function postLogin(UserLoginRequest $request, $userType = null)
    {
        $requestData = [];
        $requestData['email'] = $request->email;
        $requestData['password'] = $request->password;
        $requestData['abilities_user'] = boolval($request->abilities_user)  ?? false;
        $requestData['user_type'] = $userType;
        $requestData['remember_me'] = $request->get('remember_me');

        if (!filter_var($requestData['email'], FILTER_VALIDATE_EMAIL)) {
            $useCase = $this->loginUseCase->loginWithUsername($requestData, $this->repository);
        }else{
            $useCase = $this->loginUseCase->login($requestData, $this->repository);
        }

        if (!is_null($useCase['user'])) {
            if (in_array($useCase['user']->type, UserEnums::userCanLoginThrowBladeDashboard())) {

                flash()->success($useCase['message']);
                if ($useCase['user']->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
                    if (isset($useCase['shouldChangePassword'])){
                        return redirect()->route('auth.get.activate-manager',['confirmToken'=>$useCase['user']->confirm_token]);
                    }
                    return redirect()->to('/school-account-manager/school-account-branches');

                } elseif (
                    $useCase['user']->type == UserEnums::SCHOOL_LEADER
                    || $useCase['user']->type == UserEnums::SCHOOL_SUPERVISOR
                    || $useCase['user']->type == UserEnums::ACADEMIC_COORDINATOR) {
                    if (isset($useCase['shouldChangePassword'])){
                        return redirect()->route('auth.get.activate-manager', ['confirmToken'=>$useCase['user']->confirm_token]);
                    }
                    return redirect()->to('/school-branch-supervisor/grade-classes');
                }elseif ($useCase['user']->type == UserEnums::SCHOOL_ADMIN) {
                    if (isset($useCase['shouldChangePassword'])){
                        return redirect()->route('auth.get.activate-manager',['confirmToken'=>$useCase['user']->confirm_token]);
                    }
                    return redirect()->to('/school-admin/school-account-branches');
                }

                return redirect()->intended('/admin/dashboard');
            }
            auth()->logout();
            return redirect()->intended('/');

        } else {
            flash()->error($useCase['message']);
            return redirect()->back()->withInput();
        }
    }

    public function getForgotPassword()
    {
        $data['page_title'] = trans('auth.reset password');
        $data['module'] = $this->module;
        return view($this->module . '.resetPassword', $data);
    }

    public function postForgotPassword(ResetPasswordRequest $request)
    {
        $requestData = [];
        $requestData['email'] = $request->email;
        $useCase = $this->forgetPasswordUseCase->sendPasswordResetMail($request->email, $this->repository);
        if ($useCase['code'] == 200) {
            flash($useCase['message'])->success();
            return redirect('auth/login');
        } else {
            flash(trans($useCase['message']))->error();
            return back()->withInput();
        }
    }

    public function getUpdatePassword($token)
    {
        $row = $this->repository->findResetPasswordToken($token);
        if ($row) {
            $data['page_title'] = trans('auth.Update Password');
            $data['module'] = $this->module;
            $data['token'] = $token;
            return view($this->module . '.updatePassword', $data);
        }
        return abort(404);
    }

    public function postUpdatePassword(UpdatePasswordRequest $request, $token)
    {
        $requestData = [];
        $requestData['password'] = $request->password;
        $useCase = $this->forgetPasswordUseCase->updatePasswordUsingResetToken($token, $requestData, $this->repository);
        if ($useCase['code'] == 200) {
            flash($useCase['message'])->success();
            return redirect('auth/login');
        } else {
            flash(trans($useCase['message']))->error();
            return back()->withInput();
        }
    }

    public function getActivateSchoolAccount($confirmToken)
    {
        $data['page_title'] = trans('auth.Activate school account');
        $data['module'] = $this->module;
        $data['confirm_token'] = $confirmToken;
        $data["user"] = $this->repository->findUserByConfirmToken($confirmToken);

        return view($this->module . '.activate-manager', $data);
    }

    public function postActivateSchoolAccount(UpdatePasswordRequest $request,$confirmToken)
    {
        $data = $request->all();
        try {
            $user = $this->activateUserUseCase->activateWithSocialId($data,$confirmToken, $this->repository);
            if ($user) {
            flash('password changed successfully')->success();
            return redirect()->route('auth.get.schoolLogin');
            } else {
                flash(trans('app.Oopps The page you were looking for doesnt exist'))->error();
                return back()->withInput();
            }
        } catch (\Exception $exception) {
            flash($exception->getMessage())->error();
            return back()->withInput();
        }

    }

}
