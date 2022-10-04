<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Exam;
use Closure;
use Illuminate\Http\Request;

class PracticeMiddleware
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

        $studentId = auth()->user()->student->id;

        $studentExam = Exam::whereId($request->route('practiceId'))
            ->whereIn('type', [ExamTypes::EXAM, ExamTypes::PRACTICE])
            ->where('student_id', $studentId)->first();
        if ($studentExam) {
            return $next($request);
        }
        return unauthorize();
    }
}
