<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares;

use Closure;

use function auth;

class checkInstructorHasHomeworkMiddleware
{

    public function handle($request, Closure $next)
    {
        if (!is_null($request->route('courseHomework')) and $request->route('courseHomework')->created_by !== auth()->user()->id) {
            return unauthorize();
        }

        return $next($request);
    }
}
