<?php

namespace App\OurEdu\Subjects\Student\Middleware\Api;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Closure;

class AvailableSubjectsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $student = auth()->user()->student;
        $subject = Subject::where('grade_class_id', $student->class_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('country_id', auth()->user()->country_id)->where('id', $request->route('subjectId'))->first();

        if ($subject) {
            // if the student already subscribed to the subject
            if (!$subject->students()->where('subject_subscribe_students.student_id', $student->id)->exists()) {
                return $next($request);
            } else {
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Already subscribed to this subject',
                    'detail' => trans('subject.Already subscribed to this subject')
                ]);
            }
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'Cant subscribe to this subject',
                'detail' => trans('subject.Cant subscribe to this subject')
            ], 403);
        }
    }
}
