<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\PostAnswerUseCase;

use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\EssayQuestionUseCase\EssayQuestionPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\SatisfactionUseCase\SatisfactionPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\ScaleRatingUseCase\ScaleRatingPostAnswerInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\StarRatingUseCase\StarRatingPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Models\Assessment;
use Swis\JsonApi\Client\Collection;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use Illuminate\Support\Facades\DB;
use App\OurEdu\Assessments\Jobs\UpdateAssessmentUsersScoreJob;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MultipleChoiceUseCase\MultipleChoicePostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MatrixUseCase\MatrixPostAnswerUseCaseInterface;
class PostAnswerUseCase implements PostAnswerUseCaseInterface
{
    protected $user;
    protected $assessmentRepo;
    protected $assessmentQuestionRepo;
    protected $assessmentUsersRepo;
    protected $multipleChoiceUseCase;
    /**
     * @var ScaleRatingPostAnswerInterface
     */
    private $scaleRatingPostAnswer;
    /**
     * @var StarRatingPostAnswerUseCaseInterface
     */
    private $starRatingPostAnswerUseCase;
    /**
     * @var SatisfactionPostAnswerUseCaseInterface
     */
    private $satisfactionPostAnswerUseCase;

    /**
     * @var MatrixPostAnswerUseCaseInterface
     */
    private $matrixPostAnswerUseCase;
    /**
     * @var EssayQuestionPostAnswerUseCaseInterface
     */
    private EssayQuestionPostAnswerUseCaseInterface $essayQuestionPostAnswerUseCase;

    /**
     * PostAnswerUseCase constructor.
     * @param AssessmentRepositoryInterface $assessmentRepo
     * @param AssessmentUsersRepositoryInterface $assessmentUsersRepo
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     * @param MultipleChoicePostAnswerUseCaseInterface $multipleChoiceUseCase
     * @param ScaleRatingPostAnswerInterface $scaleRatingPostAnswer
     * @param StarRatingPostAnswerUseCaseInterface $starRatingPostAnswerUseCase
     * @param SatisfactionPostAnswerUseCaseInterface $satisfactionPostAnswerUseCase
     * @param MatrixPostAnswerUseCaseInterface $matrixPostAnswerUseCase
     * @param EssayQuestionPostAnswerUseCaseInterface $essayQuestionPostAnswerUseCase
     */
    public function __construct(
        AssessmentRepositoryInterface $assessmentRepo,
        AssessmentUsersRepositoryInterface $assessmentUsersRepo,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo,
        MultipleChoicePostAnswerUseCaseInterface $multipleChoiceUseCase,
        ScaleRatingPostAnswerInterface $scaleRatingPostAnswer,
        StarRatingPostAnswerUseCaseInterface $starRatingPostAnswerUseCase,
        SatisfactionPostAnswerUseCaseInterface $satisfactionPostAnswerUseCase,
        MatrixPostAnswerUseCaseInterface $matrixPostAnswerUseCase,
        EssayQuestionPostAnswerUseCaseInterface $essayQuestionPostAnswerUseCase
    ){
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepo = $assessmentUsersRepo;
        $this->multipleChoiceUseCase = $multipleChoiceUseCase;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
        $this->user = Auth::guard('api')->user();
        $this->type = null;
        $this->scaleRatingPostAnswer = $scaleRatingPostAnswer;
        $this->starRatingPostAnswerUseCase = $starRatingPostAnswerUseCase;
        $this->satisfactionPostAnswerUseCase = $satisfactionPostAnswerUseCase;
        $this->matrixPostAnswerUseCase = $matrixPostAnswerUseCase;
        $this->essayQuestionPostAnswerUseCase = $essayQuestionPostAnswerUseCase;
    }

    /**
     * @param int $assessmentId
     * @param Collection $data
     * @return array|void
     */
    public function postAnswer(int $assessmentId, Collection $data)
    {
        $assesseeId = $data->first()->assessee_id;
        $assessment = $this->assessmentRepo->findOrFail($assessmentId);
        if(!in_array($assesseeId,$assessment->assessees->pluck('id')->toArray())){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.invalid assessee');
            $return['title'] = 'Invalid assessee';
            return $return;
        }

        $assessorAssessment = $this->assessmentUsersRepo->findAssessorAssessment($assessmentId, $this->user->id);
        $userAssessment = $this->assessmentUsersRepo->getUserAssessment($assessmentId,$assesseeId,$this->user->id);

        $validationError = $this->validatePostAnswer($assessment,$userAssessment,$data);
        if($validationError){
            return $validationError;
        }

        foreach($data as $questionAnswerData){
            if(!isset($questionAnswerData->question_type)){
                $return['status'] = 422;
                $return['detail'] = trans('assessment.Question Type is required');
                $return['title'] = 'Question Type is required';
                return $return;
            }
            if ($questionId = $questionAnswerData->getId()) {
                $question = $this->assessmentQuestionRepo->findAssessmentQuestion($assessment, $questionId);
                $this->assessmentRepo->setAssessment($assessment);

                switch ($questionAnswerData->question_type) {
                    case (QuestionTypesEnums::SINGLE_CHOICE):
                    case (QuestionTypesEnums::MULTI_CHOICE):
                        $response = $this->multipleChoiceUseCase->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;

                    case (QuestionTypesEnums::SCALE_RATING):
                        $response = $this->scaleRatingPostAnswer->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;

                    case (QuestionTypesEnums::STAR_RATING):
                        $response = $this->starRatingPostAnswerUseCase->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;
                    case (QuestionTypesEnums::MATRIX):
                        $response = $this->matrixPostAnswerUseCase->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;

                    case (QuestionTypesEnums::SATISFICATION_RATING):
                        $response = $this->satisfactionPostAnswerUseCase->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;

                    case (QuestionTypesEnums::ESSAY_QUESTION):
                        $response = $this->essayQuestionPostAnswerUseCase->postAnswer($this->assessmentRepo, $question, $questionAnswerData->answers, $questionAnswerData);
                        break;
                    default:
                        $response['status'] = 422;
                        $response['title'] = "Question Type not Supported";
                        $response['detail'] = "Question Type not Supported";
                }


                if (isset($response['status']) && $response['status'] != 200) {
                    return $response;
                }

                $response['assessment_user_id'] = $userAssessment->id;
                $response['assessee_id'] = $assesseeId;
                $this->updateOrCreateAnswer($question, $response);
            }
        };

        $return['assessment'] = $assessment;
        $return['status'] = 200;
        return $return;
    }

    protected function updateOrCreateAnswer($question, $answerData)
    {
        $this->assessmentQuestionRepo->updateOrCreateAnswer($question, $answerData);
    }


    protected function validatePostAnswer($assessment,$userAssessment,$data){
        // assessment has not published yet
        if (is_null($assessment->published_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.cant get un published assessment');
            $return['title'] = 'cant get un published assessment';
            return $return;
        }


        // assessment time has not come yet
        if (!is_null($assessment->start_at)) {
            if (now() < $assessment->start_at) {
                $return['status'] = 422;
                $return['detail'] = trans('assessment.assessment time has not come yet');
                $return['title'] = 'assessment  time has not come yet';
                return $return;
            }
        }

        if(!is_null($assessment->end_at)){
            if(now()>$assessment->end_at){
                $return['status'] = 422;
                $return['detail'] = trans('assessment.assessment time has ended');
                $return['title'] = 'assessment time has ended';
                return $return;
            }
        }



        if (!$userAssessment || is_null($userAssessment->start_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.assessment not started yet');
            $return['title'] = 'assessment not started yet';
            return $return;
        }


        if ($userAssessment->is_finished) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.this assessment has been already taken');
            $useCase['title'] = 'this assessment has been already taken';
            return $useCase;
        }

    }
}
