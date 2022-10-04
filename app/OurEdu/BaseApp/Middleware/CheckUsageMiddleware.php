<?php

namespace App\OurEdu\BaseApp\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUsageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
