<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Student\Middleware;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ValidateHomeworkStartTime
{
    private GeneralQuizStudentRepositoryInterface $quizRepository;


    public function __construct(GeneralQuizStudentRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $studentId = auth()->user()->id;

        $generalQuizStudent = $this->quizRepository->findStudentGeneralQuiz($request->homework->id, $studentId);

        if($generalQuizStudent and $generalQuizStudent->start_at and  Carbon::now()->greaterThanOrEqualTo(Carbon::parse($request->homework->start_at))){

            return $next($request);

        }

        return formatErrorValidation([
            'status' => 422,
            'title' =>  $request->homework->quiz_type.' not started yet',
            'detail' => trans('general_quizzes.quiz not started yet',[
                'quiz_type'=> $request->homework->quiz_type
            ])
        ], 422);



    }

}
