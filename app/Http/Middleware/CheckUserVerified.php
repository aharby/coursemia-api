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
        $user = $request->user();
        if (!$user->is_verified){
            return customResponse(null,__("User not verified"), 422, StatusCodesEnum::FAILED);
        }
        return $next($request);
    }
}
