<?php

namespace App\OurEdu\Exams\Student\Middleware\Api;

use Illuminate\Support\Facades\Auth;

use function abort;

class IsStudentAllowedMiddleware
{

    public function handle($request, $next)
    {
        if ($request->route('exam') !== null) {
            if (!$request->route('exam')->courseCompetitionStudents()->find(auth()->user()->student->id)) {
                 abort(403);
            }
        }
        return $next($request);
    }
}
