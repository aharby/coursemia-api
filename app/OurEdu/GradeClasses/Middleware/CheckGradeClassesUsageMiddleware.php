<?php

namespace App\OurEdu\GradeClasses\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\GradeClasses\GradeClass;
use Closure;
use Illuminate\Http\Request;

class CheckGradeClassesUsageMiddleware extends CheckUsageMiddleware
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
        $class = GradeClass::findOrFail($request->id);
        $errorsArr = [];
        if ($class->students()->count()) {
            $errorsArr[] = trans('app.cant delete class has students under it');
        }

        if ($class->packages()->count()) {
            $errorsArr[] = trans('app.cant delete class has packages under it');
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
