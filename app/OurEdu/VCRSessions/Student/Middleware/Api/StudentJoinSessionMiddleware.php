<?php

namespace App\OurEdu\VCRSessions\Student\Middleware\Api;

use Closure;
use Illuminate\Support\Facades\DB;

class StudentJoinSessionMiddleware
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
        $student = auth()->user()->student ?? null;
        $sessionId = $request->route('sessionId');

        if($student){
            //check if student subscribed to the requested session
            $userIsSubscriped = DB::table('course_student')
                ->where('course_id', $sessionId)
                ->where('student_id', $student->id)
                ->exists();

            if($userIsSubscriped){
                return $next($request);
            }else{
                return formatErrorValidation([
                    'status' => 403,
                    'title' => 'unauthorized_action',
                    'detail' => trans('live_sessions.you have to subscribe first')
                ], 403);
            }
        }else{
            //user not student
            return unauthorize();
        }
    }
}
