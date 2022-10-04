<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;

class TypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $allowedTypes)
    {
        if ($user = Auth::user() ?? Auth::guard('api')->user()) {
            $allowedTypes = explode('|', $allowedTypes);
            if (in_array($user->type, $allowedTypes)) {
                return $next($request);
            }
        }

        // case api request
        if ($request->wantsJson()) {
            return unauthorize();
        }

        flash()->error(trans('app.User type is not authorized'));
        return redirect('/');
    }
}
