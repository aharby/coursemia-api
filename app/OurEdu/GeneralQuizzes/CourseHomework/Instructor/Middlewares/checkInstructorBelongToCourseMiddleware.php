<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares;

use Closure;

use function auth;

class checkInstructorBelongToCourseMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!is_null($request->route('course')) and $request->route('course')->instructor_id !== auth()->user()->id) {
            return unauthorize();
        }

        return $next($request);
    }
}
