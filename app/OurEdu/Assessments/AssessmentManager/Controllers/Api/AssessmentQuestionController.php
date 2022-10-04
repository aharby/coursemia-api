<?php


namespace App\OurEdu\Assessments\AssessmentManager\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Assessments\UseCases\AssessmentQuestionUseCase\AssessmentQuestionUseCaseInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\AssessmentManager\Requests\AddAssessmentQuestionRequest;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentQuestionTransformer;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Users\UserEnums;

class AssessmentQuestionController extends BaseApiController
{
    /**
     * @var AssessmentQuestionUseCaseInterface
     */
    private $assessmentQuestionUseCase;
    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;
    /**
     * @var AssessmentRepositoryInterface
     */
    private $assessmentRepo;

    /**
     * AssessmentQuestionController constructor.
     * @param AssessmentQuestionUseCaseInterface $assessmentQuestionUseCase
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     * @param AssessmentRepositoryInterface $assessmentRepo
     * @param ParserInterface $parser
     */
    public function __construct(
        AssessmentQuestionUseCaseInterface $assessmentQuestionUseCase,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo,
        AssessmentRepositoryInterface $assessmentRepo,
        ParserInterface $parser
    ) {
        $this->assessmentQuestionUseCase = $assessmentQuestionUseCase;
        $this->parser = $parser;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
        $this->assessmentRepo = $assessmentRepo;
        $this->middleware('type:assessment_manager');
    }

    /**
     * List all Assessment Questions
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Assessment $assessment)
    {
        $assessmentQuestion = $this->assessmentRepo->getAssessmentQuestions($assessment);
        return $this->transformDataModInclude($assessmentQuestion, 'actions', new AssessmentQuestionTransformer($assessment), ResourceTypesEnums::ASSESSMENT_QUESTION);
    }

    /**
     * Get assessment question
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @param App\OurEdu\Assessments\Models\AssessmentQuestion $assessmentQuestion
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Assessment $assessment, AssessmentQuestion $assessmentQuestion)
    {
        $error = $this->validateAssessment($assessment, $assessmentQuestion);
        if (count($error) > 0) {
            return formatErrorValidation($error);
        }

        $params['viewQuestion'] = true;
        return $this->transformDataModIncludeItem($assessmentQuestion, "actions", new AssessmentQuestionTransformer($assessment, $params), ResourceTypesEnums::ASSESSMENT_QUESTION);
    }

    public function store(AddAssessmentQuestionRequest $request, Assessment $assessment)
    {
        $data = $this->parser->deserialize($request->getContent())->getData();
        $assessmentQuestion = $this->assessmentQuestionUseCase->addQuestion($assessment, $data);
        if (isset($assessmentQuestion['errors'])) {
            return formatErrorValidation($assessmentQuestion['errors']);
        }
        return $this->transformDataModInclude($assessmentQuestion, 'actions', new AssessmentQuestionTransformer($assessment), ResourceTypesEnums::ASSESSMENT_QUESTION);
    }

    /**
     * Detach Question from Assessment
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @param App\OurEdu\Assessments\Models\AssessmentQuestion $assessmentQuestion
     * @return \Illuminate\Http\Response
     */
    public function delete(Assessment $assessment, AssessmentQuestion $assessmentQuestion)
    {
        $useCase = $this->assessmentQuestionUseCase->deleteQuestion($assessment, $assessmentQuestion);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('app.Deleted Successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function postCloneQuestion(Assessment $assessment, AssessmentQuestion $assessmentQuestion)
    {
        $error = $this->validateAssessment($assessment, $assessmentQuestion);
        if (count($error) > 0) {
            return formatErrorValidation($error);
        }
       $assessmentQuestion = $this->assessmentQuestionUseCase->cloneQuestion($assessmentQuestion, $assessment);
        if ($assessmentQuestion['status'] != 200) {
            return formatErrorValidation($assessmentQuestion);
        }

        return $this->transformDataModInclude($assessmentQuestion['assessmentQuestion'], 'actions', new AssessmentQuestionTransformer($assessment), ResourceTypesEnums::ASSESSMENT_QUESTION);

    }

    public function validateAssessment($assessment, $assessmentQuestion)
    {
        $error = [];
        if ($assessmentQuestion->assessment_id != $assessment->id) {
            $error['status'] = 405;
            $error['detail'] = trans("assessment.question_not_found");
            $error['title'] = 'Question Not Found';

            return $error;
        }

        return $error;
    }
}
