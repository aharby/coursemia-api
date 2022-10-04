<?php

namespace App\OurEdu\VCRSchedules\Student\Middlewares;

use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\VCRSchedules\Models\VCRSession;

class StudentVCRSessionMiddleware
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
        if ($student = Auth::guard('api')->user()->student) {
            $VCRSession = VCRSession::findOrFail($request->route('sessionId'));
            // requested Live session case
            if ($VCRSession->vcr_session_type == VCRSessionEnum::REQUESTED_LIVE_SESSION) {
                if ($VCRSession->student_id == $student->id) {
                    return $next($request);
                }
                // courses session case
            } elseif (in_array($VCRSession->vcr_session_type, [VCRSessionEnum::COURSE_SESSION_SESSION, VCRSessionEnum::LIVE_SESSION_SESSION])) {
                if ($VCRSession->participants()->where('user_id', $student->user_id)->exist()) {
                    return $next($request);
                }
                // school session case
            }elseif ($VCRSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION) {
                if ($VCRSession->classroom_id == $student->classroom_id) {
                    return $next($request);
                }
            }
        }
        throw new ErrorResponseException(trans('api.You are not related to this virtual class room session'));
    }
}
