<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use App\OurEdu\Exams\Models\Exam;
use Closure;

class CompetitionMiddleware
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

        $studentId = auth()->user()->student->id;
        $studentCompetition = Exam::findOrFail($request->competitionId)
                ->competitionStudents()
                ->where('student_id', $studentId)->exists();
        
        if ($studentCompetition) {
            return $next($request);
        }
        return unauthorize();
    }
}
