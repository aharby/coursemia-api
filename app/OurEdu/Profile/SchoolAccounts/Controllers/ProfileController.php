<?php


namespace App\OurEdu\Profile\SchoolAccounts\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Profile\SchoolAccounts\Requests\ChangePasswordRequest;
use App\OurEdu\Profile\SchoolAccounts\Requests\EditProfileRequest;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends BaseController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * ProfileController constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function edit()
    {
        $user = Auth::user();
        $page_title = trans("app.Edit My Profile");

        return view("school_supervisor.profile.edit", compact("user", "page_title"));
    }

    public function update(EditProfileRequest $request)
    {
        $user = Auth::user();

        $data = [
            "first_name" => $request->get("first_name"),
            "last_name" => $request->get("last_name"),
            "username" => $request->get("username"),
            "mobile" => $request->get("mobile"),
            "email" => $request->get("email"),
        ];

        $user = $this->userRepository->update($user, $data);

        flash()->success(trans('app.Update successfully'));
        return back();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        if (Hash::check($request->get("old_password"), $user->password)) {
            $user->password = $request->get("password");
            $user->save();

            flash()->success(trans('app.Password Changed successfully'));
            return back();
        }

        throw ValidationException::withMessages(['contradiction' => trans('app.password not Matched old Password')]);
    }
}
