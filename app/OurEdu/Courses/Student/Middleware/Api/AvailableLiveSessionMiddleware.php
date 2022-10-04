<?php

namespace App\OurEdu\Courses\Student\Middleware\Api;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Subjects\Models\Subject;
use Closure;
use Illuminate\Http\Request;

class AvailableLiveSessionMiddleware
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
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', auth()->user()->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();

        $subjectCourses = LiveSession::whereIn('subject_id', $subjects)->whereId($request->route('liveSessionId'))->first();

        $publicCourses = LiveSession::whereNull('subject_id')->where('id', $request->route('liveSessionId'))->first();
        if ($subjectCourses || $publicCourses) {
            if (!$student->subscribeCourse()->where('course_student.course_id', $request->route('liveSessionId'))->exists()) {
                return $next($request);
            } else {
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Already subscribed to this live session',
                    'detail' => trans('live_sessions.Already subscribed to this live session')
                ]);
            }
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'Cant subscribe to this live session',
                'detail' => trans('live_sessions.Cant subscribe to this live session')
            ], 403);
        }
    }
}
