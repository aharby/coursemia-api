<?php

namespace App\OurEdu\VCRSchedules\Instructor\Middlewares\Api;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use Closure;

class StudentReportMiddleware
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
        $instructor = auth()->user();
        $vcrRequest = VCRRequest::where('instructor_id' , $instructor->id)
            ->where('id' , $request->route('requestId'))
            ->first();

        if ($vcrRequest) {
            return $next($request);
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => trans('vcr.Cant View This Student Report'),
                'detail' => trans('vcr.Cant View This Student Report')
            ], 403);
        }
    }
}
