<?php

namespace App\Http\Middleware;

use App\Enums\StatusCodesEnum;
use Closure;
use Illuminate\Http\Request;

class CheckUserVerified
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
        //is_verified = is_phone_verified
        if (isset($user) && !($user->is_verified && $user->hasVerifiedEmail())) {
            return customResponse(null,__("User not verified"), 422, StatusCodesEnum::UNVERIFIED);
        }
        return $next($request);
    }
}
