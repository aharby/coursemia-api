<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use Closure;
use App\OurEdu\Exams\Models\Exam;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamTypes;

class ExamAndPracticeMiddleware
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
        $user = Auth::user() ?? Auth::guard('api')->user();
        $student = $user?->student;
        if (! ($user && $student)) {
            return unauthorize();
        }

        $studentId = $student->id;

        $studentExam = Exam::whereId($request->examId)
                    ->whereIn('type', [ExamTypes::EXAM, ExamTypes::PRACTICE])
                    ->where('student_id', $studentId)->first();
        if ($studentExam) {
            return $next($request);
        }

        return unauthorize();
    }
}
