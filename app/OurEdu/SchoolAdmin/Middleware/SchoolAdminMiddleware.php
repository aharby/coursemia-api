<?php

namespace App\OurEdu\SchoolAdmin\Middleware;

use App\OurEdu\Users\UserEnums;
use Closure;

class SchoolAdminMiddleware
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

        if($userType == UserEnums::SCHOOL_ADMIN){
            return $next($request);

        }
        return unauthorizeWeb();

    }
}
