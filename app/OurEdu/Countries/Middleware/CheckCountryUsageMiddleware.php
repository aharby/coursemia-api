<?php

namespace App\OurEdu\Countries\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Countries\Country;
use Closure;
use Illuminate\Http\Request;

class CheckCountryUsageMiddleware extends CheckUsageMiddleware
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
        $country = Country::findOrFail($request->id);
        $errorsArr = [];
        if ($country->educationalSystems()->count()) {
            $errorsArr[] = trans('app.cant delete country has educational systems under it');
        }
        if ($country->users()->count()) {
            $errorsArr[] = trans('app.cant delete country has users under it');
        }
        if ($country->gradeClasses()->count()) {
            $errorsArr[] = trans('app.cant delete country has grade classes under it');
        }
        if ($country->schools()->count()) {
            $errorsArr[] = trans('app.cant delete country has schools under it');
        }
        if ($country->packages()->count()) {
            $errorsArr[] = trans('app.cant delete country has packages under it');
        }
        if ($country->subjects()->count()) {
            $errorsArr[] = trans('app.cant delete country has subjects under it');
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
