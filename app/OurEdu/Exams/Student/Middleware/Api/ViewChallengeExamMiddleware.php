<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use App\OurEdu\Exams\Models\ExamChallenge;
use Closure;
use App\OurEdu\Exams\Models\Exam;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamTypes;

class ViewChallengeExamMiddleware
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

        if (!$user) {
            return unauthorize();
        }
        $student = $user->student;
        $exam = Exam::query()->where('id', $request->examId)
            ->withCount([
                'challenged' => function ($challenged) use ($student) {
                    $challenged->where('student_id', $student->id)
                        ->OrwhereHas('exam', function ($x) use ($student) {
                            $x->where('student_id', $student->id);
                        });
                },
                'challenges' => function ($challenges) use ($student) {
                    $challenges->where('student_id', $student->id);
                }
            ])->firstOrFail();

        if ($exam->student_id == $student->id or $exam->challenged_count or $exam->challenges_count) {
            return $next($request);
        }

        return unauthorize();
    }
}
