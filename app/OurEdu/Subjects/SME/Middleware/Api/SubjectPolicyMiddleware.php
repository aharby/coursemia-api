<?php

namespace App\OurEdu\Subjects\SME\Middleware\Api;


use App\OurEdu\Subjects\Models\Subject;
use Closure;

class SubjectPolicyMiddleware
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

        $subject=  Subject::findOrFail($request->id);
        $userId = auth()->user()->id;

        if($subject->sme_id==$userId){
            return $next($request);

        }
        return unauthorize();



    }
}
