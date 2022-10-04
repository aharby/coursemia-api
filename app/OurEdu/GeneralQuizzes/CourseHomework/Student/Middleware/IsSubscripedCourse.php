<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Student\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\OurEdu\Courses\Models\Course;

class IsSubscripedCourse
{

    public function handle($request, Closure $next)
    {
        if ($request->course_id) {
            $course = Course::find($request->course_id);
            $student = $course?->students()?->where('id', $request->user()->student->id)->first();
            if (!$student) {
                return unauthorize();
            }
        }

        return $next($request);
    }
}
