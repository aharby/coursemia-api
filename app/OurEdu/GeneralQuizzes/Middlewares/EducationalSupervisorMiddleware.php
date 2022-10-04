<?php


namespace App\OurEdu\GeneralQuizzes\Middlewares;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EducationalSupervisorMiddleware
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
        $homework = $request->route('homework');
        $user = Auth::guard('api')->user();

        if(!is_null($user->branch)){
            $branches[] = $user->branch->id;
        }else{
            $branches = $user->branches->pluck('id')->toArray() ?? [];
        }

        if(!in_array($homework->branch_id,$branches)){
            return formatErrorValidation([
                'status' => 403,
                'title' => trans('api.This Educational Supervisor not allowed edit this homework'),
                'detail' => trans('api.This Educational Supervisor not allowed edit this homework')
            ], 403);
        }
        $subjects = $user->educationalSupervisorSubjects->pluck('id')->toArray();

        if (!in_array($homework->subject_id,$subjects)) {
            return formatErrorValidation([
                'status' => 403,
                'title' => trans('api.This Educational Supervisor not allowed edit this homework'),
                'detail' => trans('api.This Educational Supervisor not allowed edit this homework')
            ], 403);
        }

        return $next($request);
    }

}
