<?php


namespace App\OurEdu\Exams\Student\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class GenerateExamAndPracticeMiddleware
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
        $checkSubject = auth()->user()->student->subjects()->where('subjects.id',
            $request->input('data.attributes.subject_id'))->first();
        if ($checkSubject) {
            return $next($request);
        }
        return unauthorize();
    }
}
