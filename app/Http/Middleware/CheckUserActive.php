<?php

namespace App\Http\Middleware;

use App\Enums\StatusCodesEnum;
use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        if (isset($user) && !$user->is_active){
            return customResponse(null,trans("api.User is suspended, please contact customer service."), 401, StatusCodesEnum::FAILED);
        }
        return $next($request);
    }
}
