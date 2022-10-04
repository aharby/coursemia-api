<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use Carbon\Carbon;
use DateTime;

class StartGeneralQuizUseCase implements StartGeneralQuizUseCaseInterface
{
    private $generalQuizRepository;
    private $generalQuizStudentRepository;
    private $userRepository;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->userRepository = $userRepository;
    }

    public function startQuiz(int $quizId)
    {
        $studentId = auth()->user()->id;
        $generalQuiz = $this->generalQuizRepository->findOrFail($quizId);

        $generalQuizStudent = $this->generalQuizStudentRepository->findStudentGeneralQuiz($quizId, $studentId);

         // further validations
         $validationErrors = $this->quizValidations($generalQuiz,$generalQuizStudent);
         if ($validationErrors) {
             return $validationErrors;
         }

        $generalQuizRepository = new GeneralQuizRepository($generalQuiz);


        // // get number of page, number of page = order of $firstNonAnsweredQuestion in quiz questions
        $quizQuestions = $generalQuiz->questions();
        //  GeneralQuizQuestionBank::where('general_quiz_id',$quizId);
        $totalNoOfQuestions = $quizQuestions->get()->count();
        $QuestionsIds = $quizQuestions->pluck('id');
        $totalNoOfAnsweredQuestions = GeneralQuizStudentAnswer::whereIn('general_quiz_question_id',$QuestionsIds)->where('student_id', $studentId)->count();

//        $pageNo = ++$totalNoOfAnsweredQuestions;
//        if($pageNo > $totalNoOfQuestions){
//            $return['status'] = 422;
//            $return['detail'] = 'Invalid Page';
//            $return['title'] = 'error getting general quiz';
//            return $return;
//        }



        $data = [
            'student_id' => $studentId,
            'subject_id' => $generalQuiz->subject_id,
            'general_quiz_id' => $quizId,
            'start_at' => now(),
            'is_reviewed' => true,
            'show_result' => $generalQuiz->show_result,
        ];



        if($generalQuizRepository->hasEssayQuestion()){
            $data['is_reviewed']=false;
        }
        $studentQuestionsOrderArray = [];
        if(!$generalQuizStudent){
            if($generalQuiz->random_question == 1){
                $studentQuestionsOrderArray = $generalQuiz->questions()
                    ->inRandomOrder()->pluck('id')->toArray();
            }
            $data['questions_order'] = $studentQuestionsOrderArray;
            if($generalQuiz->quiz_type == GeneralQuizTypeEnum::PERIODIC_TEST or $generalQuiz->quiz_type == GeneralQuizTypeEnum::FORMATIVE_TEST){
                $data['student_test_duration'] = $generalQuiz->test_time;
            }
            $generalQuizStudent = $this->generalQuizStudentRepository->create($data);
        }else{
            if(
                $generalQuiz->quiz_type == GeneralQuizTypeEnum::PERIODIC_TEST &&
                $generalQuizStudent->student_test_duration == 0
            ){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.periodic test duration has passed');
                $useCase['title'] = 'periodic test duration has passed';
                return $useCase;
            }
            if($generalQuiz->random_question == 1 && !empty($generalQuizStudent->questions_order)){
                $studentQuestionsOrderArray = $generalQuizStudent->questions_order;
            }
        }
        $questionsOrderString = implode(',', $studentQuestionsOrderArray);
        $bankQuestions = $generalQuizRepository->returnQuestion(1,$questionsOrderString);
        $return['status'] = 200;
        $return['generalQuiz'] = $generalQuiz;
        $return['bankQuestions'] = $bankQuestions;
        if ($bankQuestions->currentPage() == $bankQuestions->lastPage()) {
            $return['last_question'] = true;
        }
        $return['message'] = trans('general_quizzes.The quiz started successfully',[
            'quiz_type'=>trans('general_quizzes.'.$generalQuiz->quiz_type)
        ]);
        return $return;
    }


    private function quizValidations($generalQuiz,$generalQuizStudent)
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
       // quiz has not published yet
        if (is_null($generalQuiz->published_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.cant get un published quiz',[
                'quiz_type'=>$quizType
            ]);
            $return['title'] = 'cant get un published '.$generalQuiz->quiz_type;
            return $return;
        }

        // quiz time passed
        if (!is_null($generalQuiz->end_at)) {
            if (now() > $generalQuiz->end_at) {
                $return['status'] = 422;
                $return['detail'] = trans('general_quizzes.quiz time passed',[
                    'quiz_type'=>$quizType
                ]);
                $return['title'] = $generalQuiz->quiz_type .' time passed';
                return $return;
            }
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

        // student has been start the quiz already
        if ($generalQuizStudent) {
            if ($generalQuizStudent->is_finished){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.this quiz has been already taken',[
                    'quiz_type'=>$quizType
                ]);
                $useCase['title'] = 'this '.$generalQuiz->quiz_type.' has been already taken';
                return $useCase;
            }
        }

        // if(
        //     $generalQuizStudent &&
        //     $generalQuiz->quiz_type == GeneralQuizTypeEnum::PERIODIC_TEST &&
        //     $generalQuizStudent->student_test_duration == 0
        // ){
        //     $useCase['status'] = 422;
        //     $useCase['detail'] = trans('general_quizzes.periodic test duration has passed');
        //     $useCase['title'] = 'periodic test duration has passed';
        //     return $useCase;
        // }


    }

}
