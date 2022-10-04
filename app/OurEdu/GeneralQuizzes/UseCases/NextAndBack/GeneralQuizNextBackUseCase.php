<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\NextAndBack;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
/**
 * Next and previous question  use case
 */
class GeneralQuizNextBackUseCase implements GeneralQuizNextBackUseCaseInterface
{
    protected $user;
    protected $generalQuizRepo;
    protected $generalQuizStudentRepository;

    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepo, GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository)
    {
        $this->user = Auth::guard('api')->user();
        $this->generalQuizRepo = $generalQuizRepo;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
    }

    public function nextOrBackQuestion(int $generalQuizId, int $page)
    {
        $generalQuiz = $this->generalQuizRepo->findOrFail($generalQuizId);

        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($generalQuizId, $this->user->id);


        $generalQuizRepo = new GeneralQuizRepository($generalQuiz);


              

        $studentQuestionsOrderArray = [];
       
        if($generalQuiz->random_question == 1 && !empty($studentGeneralQuiz->questions_order)){
            $studentQuestionsOrderArray = $studentGeneralQuiz->questions_order;
        }
    
        $questionsOrderString = implode(',', $studentQuestionsOrderArray);  
        $bankQuestions = $generalQuizRepo->returnQuestion($page,$questionsOrderString);

       

        $validationError = $this->validateNextOrBackQuestion($bankQuestions,$studentGeneralQuiz,$generalQuiz);
        
        if($validationError){
            return $validationError;
        }

        if ($bankQuestions->currentPage() == $bankQuestions->lastPage()) {
                $return['last_question'] = true;
        }
    
        $questions = [];
        foreach ($bankQuestions as $question) {
            if (isset($question->question)) {
                $questions[] = $question->question;
            }
        }

        $return['status'] = 200;
        $return['questions'] = $questions;
        $return['generalQuiz'] = $generalQuiz;
        $return['bankQuestions'] = $bankQuestions;
        return $return;
    }


    private function validateNextOrBackQuestion($bankQuestions,$studentGeneralQuiz,$generalQuiz){

        $quizType = trans('general_quizzes.'.$generalQuiz->quiz_type);
        if(!$generalQuiz->is_active){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.inactive general quiz',[
                'quiz_type'=>$quizType
            ]);
            $return['title'] = $generalQuiz->quiz_type.' is not active';
            return $return;
        }

        if ($bankQuestions->currentPage() > $bankQuestions->lastPage()) {
            $return['status'] = 422;
            $return['detail'] = trans('exam.This question not found');
            $return['title'] = 'This question not found';
            return $return;
        }

        if (! $studentGeneralQuiz) {
            $return['status'] = 422;
            $return['detail'] = trans('api.Exam not started yet');
            $return['title'] = 'error not started yet';
            return $return;
        }

        if(
            $generalQuiz->quiz_type == GeneralQuizTypeEnum::PERIODIC_TEST &&
            $studentGeneralQuiz->student_test_duration == 0
        ){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.periodic test duration has passed');
            $useCase['title'] = 'periodic test duration has passed';
            return $useCase;
        }
    }
}
