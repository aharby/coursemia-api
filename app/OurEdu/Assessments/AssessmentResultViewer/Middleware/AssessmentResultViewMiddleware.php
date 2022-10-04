<?php

namespace App\OurEdu\Assessments\AssessmentResultViewer\Middleware;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentResultViewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $assessment = $request->route('assessment');
        
        $userAllowedToViewResult = $assessment->resultViewers()->where('user_id',auth()->user()->id)->first();
       
        if ($userAllowedToViewResult){
            return $next($request);
        }

        return formatErrorValidation([
            'status' => 403,
            'title' => trans('assessment.You are not allowed to view this assessment'),
            'detail' => trans('assessment.You are not allowed to view this assessment')
        ], 403);
    }
}
