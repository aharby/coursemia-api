<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase;

use App\OurEdu\BaseNotification\Jobs\UpdateGeneralQuizScoreJob;
use App\OurEdu\GeneralQuizzes\Jobs\HandelRepeatedQuestionsAnswers;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase\FinishGeneralQuizUseCaseInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;

class FinishGeneralQuizUseCase implements FinishGeneralQuizUseCaseInterface
{
    private $generalQuizRepository;
    private $generalQuizStudentRepository;
    private $userRepository;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->userRepository = $userRepository;
    }


    // 4- validate that student can finish the homework.
    public function finishGeneralQuiz(int $generalQuizId, int $studentId)
    {
        $generalQuiz = $this->generalQuizRepository->findOrFail($generalQuizId);
        $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($generalQuiz->id , $studentId);

        $error = $this->validateFinishGeneralQuiz($generalQuiz,$studentId,$studentQuiz);
        if($error){
            return $error;
        }

        $mark = $generalQuiz->mark;
        $score=$this->generalQuizStudentRepository->getStudentCorrectAnswersScore($generalQuiz->id , $studentId);
        $score_percentage = $mark ? ($score / $mark) * 100 : 0;

        $data = [
            'is_finished' => 1,
            'finish_at' => now(),
            'score_percentage'    =>  number_format($score_percentage, 2, '.', ''),
            'score' => $score
        ];
        if ($studentQuiz->is_finished != 1) {
            $this->generalQuizStudentRepository->update($studentQuiz->id, $data);
            HandelRepeatedQuestionsAnswers::dispatch($generalQuiz, $studentQuiz);

            $return['status'] = 200;
            $return['message'] = trans('api.The exam finished successfully');
            return $return;
        }
    }

    private function validateFinishGeneralQuiz($generalQuiz,$studentId,$studentQuiz)
    {

        $quizType = trans('general_quizzes.'.$generalQuiz->quiz_type);
        if(!$generalQuiz->is_active){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.inactive general quiz',[
                'quiz_type'=>$quizType
            ]);
            $return['title'] = $generalQuiz->quiz_type.' is not active';
            return $return;
        }

        // validate that if student chose to finish the homework and there are unsloved questions to be alerted by that there are questions not answered yet.
        // validate that student cannot finish homework unless he answered all the homework questions.
        if(!$studentQuiz){
            $return['status'] = 422;
            $return['detail'] = 'General Quiz Didn\'t Started yet';
            $return['title'] = 'quiz_didnt_start_yet';
            return $return;
        }

        $generalQuizQuestionsCount = $generalQuiz->questions()->count();
        $studentAnswersCount =  $this->generalQuizStudentRepository->getStudentTotalAnswersCount($generalQuiz->id,$studentId);

        if($generalQuizQuestionsCount > $studentAnswersCount && !request()->has('force_finish') ){
            $return['status'] = 422;
            $return['detail'] = trans('api.cannot finish homework unless he answered all the homework questions');
            $return['title'] = 'can_not_finish';
            return $return;
        }

        if ($studentQuiz->is_finished == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is already finished');
            $return['title'] = 'exam_already_finished';
            return $return;
        }
    }
}
