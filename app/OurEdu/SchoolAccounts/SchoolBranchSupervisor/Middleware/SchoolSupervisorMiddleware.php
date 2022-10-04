<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware;


use App\OurEdu\Users\UserEnums;
use Closure;

class SchoolSupervisorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $userType = auth()->user()->type;

        if($userType == UserEnums::SCHOOL_SUPERVISOR
            || $userType == UserEnums::SCHOOL_LEADER
            || $userType == UserEnums::ACADEMIC_COORDINATOR
            || $userType == UserEnums::SCHOOL_ACCOUNT_MANAGER
            || $userType == UserEnums::SCHOOL_ADMIN
        ){
            return $next($request);
        }
        return unauthorize();

    }
}
