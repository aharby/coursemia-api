<?php

namespace App\OurEdu\VCRSchedules\Instructor\Middlewares\Api;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use Closure;

class AcceptRequestMiddleware
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
        $instructor = auth()->user()->instructor;
        $request = VCRRequest::where('instructor_id' , $instructor->id)->where('id' , request()->route('requestId'))->first();

        if ($request) {
                if ($request->accepted_at) {
                    return formatErrorValidation([
                        'status' => 422,
                        'title' => trans('vcr.already accepted request'),
                        'detail' => trans('vcr.already accepted request'),
                    ]);
                }
          return $next(request());
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => trans('vcr.Cant not Accept this Request'),
                'detail' => trans('vcr.Cant not Accept this Request')
            ], 403);
        }
    }
}
