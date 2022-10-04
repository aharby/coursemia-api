<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSuspense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->suspended_at != null) {
            Auth::logout();
            return redirect('/auth/login')->withErrors('Your account Suspended, Please Call the Admin.');
        }
        return $next($request);
    }
}
