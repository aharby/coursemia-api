<?php

namespace App\OurEdu\Subjects\SME\Middleware\Api;

use App\OurEdu\Users\UserEnums;
use Closure;

class TaskMiddleware
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
        $user = auth()->user();
        if($user->type == UserEnums::SME_TYPE || $user->type == UserEnums::CONTENT_AUTHOR_TYPE ){
            return $next($request);
        }
        return unauthorize();
    }
}
