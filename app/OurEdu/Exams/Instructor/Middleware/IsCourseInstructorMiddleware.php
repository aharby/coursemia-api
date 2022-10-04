<?php

namespace App\OurEdu\Exams\Instructor\Middleware;

use Illuminate\Support\Facades\Auth;

use function abort;

class IsCourseInstructorMiddleware
{

    public function handle($request, $next)
    {
        if (Auth::user()->id !== $request->route('course')->instructor_id) {
            return abort(403);
        }
        return $next($request);
    }
}
