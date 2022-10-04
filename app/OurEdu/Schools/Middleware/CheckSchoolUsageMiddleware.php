<?php

namespace App\OurEdu\Schools\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Schools\School;
use Closure;
use Illuminate\Http\Request;

class CheckSchoolUsageMiddleware extends CheckUsageMiddleware
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
        $school = School::findOrFail($request->id);
        $errorsArr = [];
        if ($school->students()->count()) {
            $errorsArr[] = trans('app.cant delete school has students under it');
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
