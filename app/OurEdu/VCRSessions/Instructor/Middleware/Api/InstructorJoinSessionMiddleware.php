<?php

namespace App\OurEdu\VCRSessions\Instructor\Middleware\Api;

use Closure;
use App\OurEdu\Courses\Models\SubModels\LiveSession;

class InstructorJoinSessionMiddleware
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
        $instructor = auth()->user()->instructor ?? null;
        $sessionId = $request->route('sessionId');

        if($instructor){
            //check if instructor own the requested session
            $liveSession = LiveSession::findOrfail($sessionId);

            if($liveSession->instructor_id == auth()->user()->id){
                return $next($request);
            }else{
                return formatErrorValidation([
                    'status' => 403,
                    'title' => 'unauthorized_action',
                    'detail' => trans('live_sessions.you do not own this session')
                ], 403);
            }
        }else{
            //user not instructor
            return unauthorize();
        }
    }
}
