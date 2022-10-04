<?php

namespace App\OurEdu\SchoolAccounts\Middleware;


use App\OurEdu\Users\UserEnums;
use Closure;

class SchoolAccountBranchAdminMiddleware
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

        if($userType == UserEnums::SUPER_ADMIN_TYPE){
            return $next($request);

        }
        return unauthorize();

    }
}
