<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\EssayPostAnswerUseCase\EssayPostAnswerUseCaseInterface;
use Swis\JsonApi\Client\Collection;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseNotification\Jobs\UpdateGeneralQuizScoreJob;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerWithCorrectUseCase\TrueFalsePostAnswerWithCorrectUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\SingleChoicePostAnswerUseCase\SingleChoicePostAnswerUseCaseInterface;
class PostAnswerUseCase implements PostAnswerUseCaseInterface
{
    protected $user;
    protected $generalQuizRepository;
    protected $generalQuizQuestionRepository;
    protected $generalQuizStudentRepository;
    protected $trueFalsePostAnswerUseCase;
    protected $multiChoicePostAnswerUseCase;
    protected $singleChoicePostAnswerUseCase;
    // protected $answerMethods = [];
    protected $trueFalsePostAnswerWithCorrectUseCase;
    protected $essayPostAnswerUseCase;
    protected $dragDropPostAnswerUseCase;
    protected $completePostAnswerUseCase;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        QuestionBankRepositoryInterface $generalQuizQuestionRepository,
        TrueFalsePostAnswerUseCaseInterface $trueFalsePostAnswerUseCase,
        TrueFalsePostAnswerWithCorrectUseCaseInterface $trueFalsePostAnswerWithCorrectUseCase,
        MultiChoicePostAnswerUseCaseInterface $multiChoicePostAnswerUseCase,
        SingleChoicePostAnswerUseCaseInterface $singleChoicePostAnswerUseCase,
        EssayPostAnswerUseCaseInterface $essayPostAnswerUseCase,
        DragDropPostAnswerUseCaseInterface $dragDropPostAnswerUseCase,
        CompletePostAnswerUseCaseInterface $completePostAnswerUseCase
    ){
        $this->generalQuizRepository = $generalQuizRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->generalQuizQuestionRepository = $generalQuizQuestionRepository;
        $this->trueFalsePostAnswerUseCase = $trueFalsePostAnswerUseCase;
        $this->multiChoicePostAnswerUseCase = $multiChoicePostAnswerUseCase;
        $this->singleChoicePostAnswerUseCase = $singleChoicePostAnswerUseCase;
        $this->trueFalsePostAnswerWithCorrectUseCase = $trueFalsePostAnswerWithCorrectUseCase;
        $this->essayPostAnswerUseCase = $essayPostAnswerUseCase;
        $this->dragDropPostAnswerUseCase = $dragDropPostAnswerUseCase;
        $this->completePostAnswerUseCase = $completePostAnswerUseCase;
        $this->user = Auth::guard('api')->user();
        $this->type = null;
    }

    /**
     * @param int $quizId
     * @param Collection $data
     * @return array|void
     */
    public function postAnswer(int $quizId, Collection $data)
    {


        $generalQuiz = $this->generalQuizRepository->findOrFail($quizId);
        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($quizId, $this->user->id);


        $validationError = $this->validatePostAnswer($generalQuiz,$studentGeneralQuiz,$data);
        if($validationError){
            return $validationError;
        }

        foreach($data as $questionAnswerData){
            if(!isset($questionAnswerData->question_type)){
                $return['status'] = 422;
                $return['detail'] = trans('general_quizzes.Question Type is required');
                $return['title'] = 'Question Type is required';
                return $return;
            }
            if ($questionId = $questionAnswerData->getId()) {
                $questionBank = $this->generalQuizQuestionRepository->findGeneralQuizQuestion($generalQuiz, $questionId);
                $this->generalQuizRepository->setGeneralQuiz($generalQuiz);
                switch ($questionAnswerData->question_type) {

                    case (QuestionsTypesEnums::TRUE_FALSE):
                        $response = $this->trueFalsePostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;

                    case (QuestionsTypesEnums::MULTI_CHOICE):
                        $response = $this->multiChoicePostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;

                    case (QuestionsTypesEnums::SINGLE_CHOICE):
                        $response = $this->singleChoicePostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;

                    case (QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT):
                        $response = $this->trueFalsePostAnswerWithCorrectUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;
                    case (QuestionsTypesEnums::ESSAY):
                        $response = $this->essayPostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;

                    case (QuestionsTypesEnums::DRAG_DROP_TEXT):
                    case (QuestionsTypesEnums::DRAG_DROP_IMAGE):
                        $response = $this->dragDropPostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                    break;

                    case (QuestionsTypesEnums::COMPLETE):
                        $response = $this->completePostAnswerUseCase->postAnswer($this->generalQuizRepository, $questionBank, $questionAnswerData->answers);
                        break;
                    default:
                        $response['status'] = 422;
                        $response['title']= "Question Type not Supported";
                        $response['detail'] = "Question Type not Supported";
                }


                if(isset($response['status']) && $response['status'] !=200){
                    return $response;
                }



                $this->updateOrCreateAnswer($questionBank, $response);

            }
        };

        if($questionAnswerData->question_type != QuestionsTypesEnums::ESSAY){
            UpdateGeneralQuizScoreJob::dispatch($generalQuiz,$studentGeneralQuiz)->onQueue('low')->onConnection('redisOneByOne');
        }

        $return['generalQuiz'] = $generalQuiz;
        $return['status'] = 200;
        return $return;
    }

    protected function updateOrCreateAnswer($question, $generalQuizAnswerData)
    {
        $this->generalQuizQuestionRepository->updateOrCreateAnswer($question, $generalQuizAnswerData);
    }


    protected function validatePostAnswer($generalQuiz,$studentGeneralQuiz,$data){
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

        if(!is_null($generalQuiz->end_at)){
            if(now()>$generalQuiz->end_at){
                $return['status'] = 422;
                $return['detail'] = trans('general_quizzes.quiz time has ended',[
                    'quiz_type'=>$quizType
                ]);
                $return['title'] = $generalQuiz->quiz_type .'  time has ended';
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
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.this quiz has been already taken',[
                'quiz_type'=>$quizType
            ]);
            $useCase['title'] = 'this '.$generalQuiz->quiz_type.' has been already taken';
            return $useCase;
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
