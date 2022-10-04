<?php


namespace App\OurEdu\SchoolAdmin\Profile\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\Profile\ChangePasswordRequest;
use App\OurEdu\SchoolAdmin\Profile\Requests\EditProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends BaseController
{

    public function __construct()
    {
    }

    public function edit()
    {
        $user = Auth::user();
        $page_title = trans("app.Edit My Profile");
        return view("school_admin.profile.edit", compact("user", "page_title"));
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

        $user->update($data);
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

    public function updateCurrentSchool($schoolId)
    {
        $school = SchoolAccount::findOrFail($schoolId);
        if ($school) {
            \auth()->user()->schoolAdmin->update([
                'current_school_id' => $schoolId
            ]);
            flash()->success(trans('app.Current School Changed successfully'));
        }
        flash()->success(trans('app.Something went wrong'));
        return redirect()->route('school-admin.school-account-branches.get.index');
    }
}
