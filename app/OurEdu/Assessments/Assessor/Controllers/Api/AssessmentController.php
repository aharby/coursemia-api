<?php


namespace App\OurEdu\Assessments\Assessor\Controllers\Api;

use App\OurEdu\Assessments\Assessor\Transformers\AssessmentQuestionTransformer;
use App\OurEdu\Assessments\AssessmentManager\Requests\GeneralCommentRequest;
use App\OurEdu\Assessments\Assessor\Transformers\AssessmentAssesseeTransformer;
use App\OurEdu\Assessments\Assessor\Transformers\AssessmentTransformer;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase\StartAssessmentUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\FinishAssessmentUseCase\FinishAssessmentUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\NextAndBack\AssessmentNextBackUseCaseInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class AssessmentController extends BaseApiController
{
    /**
     * @var ParserInterface
     */
    private $parserInterface;

    /**
     * @var StartAssessmentUseCaseInterface
     */
    private $startAssessmentUseCase;

    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;


    /**
     * @var PostAnswerUseCaseInterface
    */
    private $postAnswerUseCase;


    /**
     * @var FinishAssessmentUseCaseInterface
     */
    private $finishAssessmentUseCase;


    /**
     * @var AssessmentNextBackUseCaseInterface
     */
    private $nextBackUseCase;
    /**
     * AssessmentController constructor.
     * @param StartAssessmentUseCaseInterface $StartAssessmentUseCase
     * @param AssessmentUsersRepositoryInterface $assessmentUsersRepository
     * @param ParserInterface $parserInterface
     * @param PostAnswerUseCaseInterface $postAnswerUseCase
     * @param FinishAssessmentUseCaseInterface $finishAssessmentUseCase
     * @param AssessmentNextBackUseCaseInterface $nextBackUseCase
    */


    /**
     * AssessmentController constructor.
     */
    public function __construct(
        AssessmentUsersRepositoryInterface $assessmentUsersRepository,
        StartAssessmentUseCaseInterface $startAssessmentUseCase,
        ParserInterface $parserInterface,
        PostAnswerUseCaseInterface $postAnswerUseCase,
        FinishAssessmentUseCaseInterface $finishAssessmentUseCase,
        AssessmentNextBackUseCaseInterface $nextBackUseCase
    ){
        $this->parserInterface = $parserInterface;
        $this->startAssessmentUseCase = $startAssessmentUseCase;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
        $this->postAnswerUseCase = $postAnswerUseCase;
        $this->finishAssessmentUseCase = $finishAssessmentUseCase;
        $this->nextBackUseCase = $nextBackUseCase;
    }


    public function index()
    {
        $assessments = $this->assessmentUsersRepository->getAssessmentsByAssessor(Auth::user());

        return $this->transformDataModInclude($assessments, "actions", new AssessmentTransformer(), ResourceTypesEnums::ASSESSMENT);
    }


    public function startAssessment($assessmentId,$assesseeId){
        $usecase = $this->startAssessmentUseCase->startAssessment($assessmentId,$assesseeId);
        if ($usecase['status'] == 200) {
            $params["assessee"] = User::query()->findOrFail($assesseeId);
            $params["assessor"] = Auth::user();
            $params['question'] = $usecase['questions'];
            $params['startAssessment'] = true;
            $assessment = $usecase['assessment'];
            if (isset($usecase['last_question'])) {
                $params['finish_assessment'] = true;
            }
            $meta['message'] = $usecase['message'];
            $params['nextBack'] = true;
            return $this->transformDataModInclude($params['question'], 'questions', new AssessmentQuestionTransformer($assessment, $params), ResourceTypesEnums::ASSESSMENT_QUESTION,$meta);
        } else {
            return formatErrorValidation($usecase);
        }
    }


    public function postAnswer(BaseApiRequest $request, Assessment $assessment)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->postAnswerUseCase->postAnswer($assessment->id, $data);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);

        } else {
            return formatErrorValidation($useCase);
        }
    }



    public function getNextOrBackQuestion(Assessment $assessment,$assesseeId)
    {
        $page = request('page') ?? 1;
        $usecase = $this->nextBackUseCase->nextOrBackQuestion($assessment->id,$assesseeId, $page);

        if ($usecase['status'] == 200) {
            $params["assessor"] = Auth::user();
            $params["assessee"] = User::query()->findOrFail($assesseeId);
            $assessment = $usecase['assessment'];
            $questions = $usecase['questions'];
            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            $params['nextBack'] = true;
            return $this->transformDataModInclude($questions, 'questions', new AssessmentQuestionTransformer($assessment, $params), ResourceTypesEnums::ASSESSMENT_QUESTION);
        } else {
            return formatErrorValidation($usecase);
        }
    }


    public function finishAssessment(Assessment $assessment,$assesseeId)
    {
        $assessorId = auth()->user()->id;

        $usecase = $this->finishAssessmentUseCase->finishAssessment($assessment->id , $assessorId,$assesseeId);

        if ($usecase['status'] == 200) {

            $assessorAssessment = $this->assessmentUsersRepository->findAssessorAssessment($assessment->id , $assessorId);
            return response()->json([
                'meta' => [
                    'message' => trans('assessment.Finished successfully')
                ]
            ]);
            // return $this->transformDataModIncludeItem($assessorAssessment, "", new FeedbackTransformer(),ResourceTypesEnums::ASSESSMENT_FEEDBACK);
        } else {
            return formatErrorValidation($usecase);
        }
    }
    public function postGeneralComment(GeneralCommentRequest $request, Assessment $assessment,$assesseeId)
    {
        $userAssessment = $this->assessmentUsersRepository->getUserAssessment($assessment->id,$assesseeId,Auth::user()->id);

        $validation = $this->validatePostGeneralComment($assessment, $userAssessment);

        if ($validation)
        {
            return formatErrorValidation($validation);
        }

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $userAssessment->update( ["general_comment" => $data->comment ?? ""]);

        return response()
            ->json([
                "meta" => [
                    "message" => trans("assessment.Comment Added Successfully"),
                ],
            ]);
    }

    protected function validatePostGeneralComment(Assessment $assessment, $userAssessment)
    {
        if (!$assessment->has_general_comment){
            $error['status'] = 422;
            $error['detail'] = trans("assessment.The Assessment Does not need The Comment" );
            $error['title'] = "The Assessment Does not need The Comment";
            return $error;
        }

        // assessment has not published yet
        if (is_null($assessment->published_at)) {
            $error['status'] = 422;
            $error['detail'] = trans('assessment.cant get un published assessment');
            $error['title'] = 'cant get un published assessment';
            return $error;
        }

        // assessment time has not come yet
        if (!is_null($assessment->start_at)) {
            if (now() < $assessment->start_at) {
                $error['status'] = 422;
                $error['detail'] = trans('assessment.assessment time has not come yet');
                $error['title'] = 'assessment  time has not come yet';
                return $error;
            }
        }

        if(!is_null($assessment->end_at)){
            if(now()>$assessment->end_at){
                $error['status'] = 422;
                $error['detail'] = trans('assessment.assessment time has ended');
                $error['title'] = 'assessment time has ended';
                return $error;
            }
        }

        if (is_null($userAssessment->start_at)) {
            $error['status'] = 422;
            $error['detail'] = trans('assessment.assessment not started yet');
            $error['title'] = 'assessment not started yet';
            return $error;
        }

        if ($userAssessment->is_finished) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.this assessment has been already taken');
            $useCase['title'] = 'this assessment has been already taken';
            return $useCase;
        }
    }

    public function listAssessorAssesses(Assessment $assessment)
    {
        $assessees = $this->assessmentUsersRepository->getAssessorAssessees($assessment, auth()->user());

        return $this->transformDataModInclude($assessees, "actions", new AssessmentAssesseeTransformer($assessment), ResourceTypesEnums::ASSESSMENT_ASSESSEE);
    }

}


