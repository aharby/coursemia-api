<?php


namespace App\OurEdu\GeneralQuizzes\Middlewares;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class checkInstructorMiddleware
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
        $homeworkId = $request->route('homeworkId');
        if($homeworkId){
            $generalQuiz = GeneralQuiz::findOrFail($homeworkId);
        }

        $periodicTestId = $request->route('periodicTestId');
        if($periodicTestId){
            $generalQuiz = GeneralQuiz::findOrFail($periodicTestId);
        }
        $user = Auth::guard('api')->user();

        if($generalQuiz->created_by == $user->id){
            return $next($request);
        }
        return $next($request);
//        return formatErrorValidation([
//            'status' => 403,
//            'title' =>'not allowed to edit this '.$generalQuiz->quiz_type,
//            'detail' => trans('api.This Instructor not allowed edit this quiz',[
//                'quizType'=>trans('general_quizzes.'.$generalQuiz->quiz_type)
//            ])
//        ], 403);
    }

}
