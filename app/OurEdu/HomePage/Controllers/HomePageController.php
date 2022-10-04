<?php

namespace App\OurEdu\HomePage\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Exams\Events\CompetitionEvents\CompetitionQuestionAnswered;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;

class HomePageController extends BaseController
{
    public function index()
    {
        return view('welcome');
    }

    public function home()
    {
        $user = Auth::user();
        if ($user){
            switch ($user->type) {
                case UserEnums::ADMIN_TYPE:
                    return redirect(env('ADMIN_PORTAL_URL') . '/' . lang() . '/admin/dashboard');
                case UserEnums::SCHOOL_ACCOUNT_MANAGER:
                    return redirect(env('ADMIN_PORTAL_URL') . '/' . lang() . '/school-account-manager/school-account-branches');

                case UserEnums::SCHOOL_SUPERVISOR:
                case UserEnums::SCHOOL_LEADER:
                    return redirect(env('ADMIN_PORTAL_URL') . '/' . lang() . '/school-branch-supervisor/grade-classes');
                case UserEnums::SME_TYPE:
                    return redirect(env('CONTENT_AUTHOR_PORTAL_URL'));
                case UserEnums::CONTENT_AUTHOR_TYPE:
                    return redirect(env('CONTENT_AUTHOR_PORTAL_URL'));
                case UserEnums::STUDENT_TYPE:
                case UserEnums::STUDENT_TEACHER_TYPE:
                case UserEnums::INSTRUCTOR_TYPE:
                case UserEnums::SCHOOL_INSTRUCTOR:
                case UserEnums::PARENT_TYPE:
                    return redirect(env('STUDENT_PORTAL_URL'));
            }
        }
        return redirect('/');
    }
}
