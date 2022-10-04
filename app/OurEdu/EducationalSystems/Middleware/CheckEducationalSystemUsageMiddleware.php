<?php

namespace App\OurEdu\EducationalSystems\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Closure;
use Illuminate\Http\Request;

class CheckEducationalSystemUsageMiddleware extends CheckUsageMiddleware
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
        $educationalSystem = EducationalSystem::findOrFail($request->id);
        $errorsArr = [];
        if ($educationalSystem->subjects()->count()) {
            $errorsArr[] = trans('app.cant delete educational system has subjects under it');
        }
        if ($educationalSystem->schools()->count()) {
            $errorsArr[] = trans('app.cant delete educational system has schools under it');
        }
        if ($educationalSystem->students()->count()) {
            $errorsArr[] = trans('app.cant delete educational system has students under it');
        }
        if ($educationalSystem->packages()->count()) {
            $errorsArr[] = trans('app.cant delete educational system has packages under it');
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
