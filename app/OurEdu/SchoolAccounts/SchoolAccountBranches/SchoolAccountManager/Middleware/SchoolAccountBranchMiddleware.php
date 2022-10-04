<?php

namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Middleware;

use App\OurEdu\Users\UserEnums;
use Closure;

class SchoolAccountBranchMiddleware
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

        if($userType == UserEnums::SCHOOL_ACCOUNT_MANAGER){
            return $next($request);

        }
        return unauthorizeWeb();

    }
}
