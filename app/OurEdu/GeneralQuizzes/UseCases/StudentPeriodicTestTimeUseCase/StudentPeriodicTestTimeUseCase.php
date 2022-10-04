<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\StudentPeriodicTestTimeUseCase;

use Swis\JsonApi\Client\Collection;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Jobs\UpdateStudentPeriodicTestTimeJob;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;

class StudentPeriodicTestTimeUseCase implements StudentPeriodicTestTimeUseCaseInterface
{
    protected $user;
    protected $generalQuizRepository;
    protected $generalQuizStudentRepository;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
    ){
        $this->generalQuizRepository = $generalQuizRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->user = Auth::guard('api')->user();
    }

    /**
     * @param GeneralQuiz $periodicTest
     */
    public function updateStudentPeriodicTestTime(GeneralQuiz $periodicTest,$data)
    {

        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($periodicTest->id, $this->user->id);

        $validationError = $this->validateUpdatePeriodicTestTime($periodicTest,$studentGeneralQuiz,$data);

        if($validationError){
            return $validationError;
        }

        UpdateStudentPeriodicTestTimeJob::dispatch(
            $periodicTest,$this->generalQuizStudentRepository,
            $this->user,$data->student_test_duration
        )->onQueue('low')->onConnection('redisOneByOne');

        $return['status'] = 200;
        return $return;
    }



    protected function validateUpdatePeriodicTestTime($generalQuiz,$studentGeneralQuiz,$data){
        $quizType = $generalQuiz->quiz_type;
        $return = [];
        if (!in_array($generalQuiz->quiz_type, [GeneralQuizTypeEnum::PERIODIC_TEST, GeneralQuizTypeEnum::FORMATIVE_TEST])) {
            $return['status'] = 404;
            $return['detail'] = trans('general_quizzes.Periodic test not found');
            $return['title'] = 'Periodic test not found';
            return $return;
        }

        if($data->student_test_duration > $studentGeneralQuiz->student_test_duration){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.student test duration can not be greater than periodic test time limit');
            $return['title'] = 'invalid student_test_duration';
            return $return;
        }
        // quiz time has not come yet
        if (!is_null($generalQuiz->start_at)) {
            if (now() < $generalQuiz->start_at) {
                $return['status'] = 422;
                $return['detail'] = trans('general_quizzes.quiz time has not come yet',[
                    'quiz_type'=>$quizType
                ]);
                $return['title'] = $generalQuiz->quiz_type .'  time has not come yet';
                return $return;
            }
        }

        if (!$studentGeneralQuiz) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.quiz not started yet',[
                'quiz_type'=>$quizType
            ]);
            $return['title'] = $generalQuiz->quiz_type.' not started yet';
            return $return;
        }


        if ($studentGeneralQuiz->is_finished) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.this quiz has been already taken',[
                'quiz_type'=>$quizType
            ]);
            $return['title'] = 'this '.$generalQuiz->quiz_type.' has been already taken';
            return $return;
        }

    }

    public function getStudentPeriodicTestTimeLeft(GeneralQuiz $periodicTest){
        $studentPeriodicTest = $this->generalQuizStudentRepository
                                    ->findStudentGeneralQuiz($periodicTest->id,auth()->user()->id);
        return $studentPeriodicTest->student_test_duration;
    }
}
