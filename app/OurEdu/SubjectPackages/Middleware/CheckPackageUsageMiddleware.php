<?php

namespace App\OurEdu\SubjectPackages\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\SubjectPackages\Package;
use Closure;
use Illuminate\Http\Request;

class CheckPackageUsageMiddleware extends CheckUsageMiddleware
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
        $package = Package::findOrFail($request->id);
        $errorsArr = [];
        if ($package->students()->count()) {
            $errorsArr[] = trans('app.cant delete course has students subscribed to it');
        }

        if (count($errorsArr)){
            foreach ($errorsArr as $error) {
                flash()->error($error);
            }
            return back();
        }
        return $next($request);
    }
}
