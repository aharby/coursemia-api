<?php


namespace App\OurEdu\Quizzes\Student\Middleware;


use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use Illuminate\Http\Request;

class QuizAuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {

        $studentId = auth()->user()->student->id;

        $quizId = $request->route('quizId') ?? $request->route('homeworkId') ?? $request->route('periodicTestId');

        $studentQuiz = StudentQuiz::where('student_id', $studentId)
            ->where('quiz_id', $quizId)->first();

        if ($studentQuiz && $studentQuiz->status != QuizStatusEnum::FINISHED) {
            return $next($request);
        }
        return unauthorize();
    }
}
