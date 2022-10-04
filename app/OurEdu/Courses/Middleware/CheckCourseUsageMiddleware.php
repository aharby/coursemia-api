<?php

namespace App\OurEdu\Courses\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Courses\Models\Course;
use Closure;
use Illuminate\Http\Request;

class CheckCourseUsageMiddleware extends CheckUsageMiddleware
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
        $course = Course::findOrFail($request->id);
        $errorsArr = [];
        if ($course->students()->count()) {
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
