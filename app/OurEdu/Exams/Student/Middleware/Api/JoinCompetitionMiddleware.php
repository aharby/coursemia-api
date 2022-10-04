<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use App\OurEdu\Exams\Models\Exam;
use Closure;
use Illuminate\Http\Request;

class JoinCompetitionMiddleware
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
        try {
            $competition = Exam::findOrFail($request->competitionId);
            $checkSubject = auth()->user()
                ->student->subjects()
                ->where('subjects.id', $competition->subject_id)
                ->first();
            if ($checkSubject) {
                return $next($request);
            }
        } catch (\Throwable $exception) {
            return unauthorize();
        }
        return unauthorize();
    }
}
