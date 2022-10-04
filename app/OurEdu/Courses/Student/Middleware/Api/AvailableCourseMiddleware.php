<?php

namespace App\OurEdu\Courses\Student\Middleware\Api;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use Closure;
use Illuminate\Http\Request;

class AvailableCourseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $student = auth()->user()->student;

        $subjects = Subject::query()
            ->where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', auth()->user()->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();
        $subjectCourses = Course::whereIn('subject_id', $subjects)->whereId($request->route('courseId'))->first();
        $publicCourses = Course::where('subject_id', null)->where('id',
            $request->route('courseId'))->first();
        if ($subjectCourses || $publicCourses) {
            if (!$student->subscribeCourse()->where('course_student.course_id', $request->route('courseId'))->exists()) {
                return $next($request);
            } else {
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Already subscribed to this course',
                    'detail' => trans('course.Already subscribed to this course')
                ]);
            }
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'Cant subscribe to this course',
                'detail' => trans('course.Cant subscribe to this course')
            ], 403);
        }
    }
}
