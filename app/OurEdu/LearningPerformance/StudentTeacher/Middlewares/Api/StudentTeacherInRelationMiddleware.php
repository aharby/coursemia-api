<?php


namespace App\OurEdu\LearningPerformance\StudentTeacher\Middlewares\Api;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use Closure;

class StudentTeacherInRelationMiddleware
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

        $studentTeacher = auth()->user();
        $student = Student::findOrFail($request->studentId);
        $inRelation = $studentTeacher->supervisedStudents()
            ->where('student_id', $student->user->id)
            ->exists();
        if ($inRelation) {
            return $next($request);
        }
        return unauthorize();
    }
}
