<?php

namespace App\OurEdu\Subscribes\Parent\Middleware;
use App\OurEdu\Users\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentChildMiddleware
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
        $childId = $request->route('studentId');
        $student = Student::findOrFail($childId);
        $user = Auth::guard('api')->user();
        $IsParentHasChild = $user->students()->where('id', $student->user_id)->exists();

        if ($IsParentHasChild){
            return $next($request);
        }

        return formatErrorValidation([
            'status' => 403,
            'title' => trans('api.You are not related to this child'),
            'detail' => trans('api.You are not related to this child')
        ], 403);
    }
}
