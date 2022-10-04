<?php


namespace App\OurEdu\Assessments\AssessmentManager\Controllers\Api;

use App\OurEdu\Assessments\AssessmentManager\Requests\AssessmentPointsRateRequest;
use App\OurEdu\Assessments\AssessmentManager\Requests\CloneAssessmentRequest;
use App\OurEdu\Assessments\UseCases\CloneAssessment\CloneAssessmentUseCaseInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Assessments\AssessmentManager\Requests\CreateAssessmentRequest;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentPointsRateTransformer;
use App\OurEdu\Assessments\UseCases\CreateAssessmentUseCase\CreateAssessmentUseCaseInterface;
use App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase\UpdateAssessmentUseCaseInterface;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\QuestionViewAsAssessorTransformer;
use App\OurEdu\Assessments\AssessmentManager\UseCases\ViewAsAssessorUseCase\ViewAsAssessorUseCaseInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\UseCases\AssessmentPointsRate\AssessmentPointRateUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;

class AssessmentController  extends BaseApiController
{
    /**
     * @var CreateAssessmentUseCaseInterface
     */
    protected $createAssessmentUseCase;

    /**
     * @var UpdateAssessmentUseCaseInterface
     */
    protected $updateAssessmentUseCase;


    /**
     * @var UpdateAssessmentUseCaseInterface
     */
    protected $cloneAssessmentUseCase;

    /**
     * @var ParserInterface
     */
    private $parserInterface;

    /**
     * @var AssessmentRepositoryInterface
     */
    private $assessmentRepo;

    /**
     * @var AssessmentPointRateUseCaseInterface
     */
    private $assessmentRateUseCase;

    /**
     * @var ViewAsAssessorUseCaseInterface
     */
    private $viewAsAssessorUseCase;


    private $filters = [];
    /**
     * AssessmentController constructor.
     * @param CreateAssessmentUseCaseInterface $createAssessmentUseCase
     * @param UpdateAssessmentUseCaseInterface $updateAssessmentUseCase
     * @param ParserInterface $parserInterface
     * @param AssessmentRepositoryInterface $assessmentRepo

     */

    public function __construct(
        CreateAssessmentUseCaseInterface $createAssessmentUseCase,
        UpdateAssessmentUseCaseInterface $updateAssessmentUseCase,
        CloneAssessmentUseCaseInterface $cloneAssessmentUseCase,
        ParserInterface $parserInterface,
        AssessmentRepositoryInterface $assessmentRepo,
        AssessmentPointRateUseCaseInterface $assessmentRateUseCase,
        ViewAsAssessorUseCaseInterface $viewAsAssessorUseCase
    ) {
        $this->createAssessmentUseCase = $createAssessmentUseCase;
        $this->updateAssessmentUseCase = $updateAssessmentUseCase;
        $this->cloneAssessmentUseCase = $cloneAssessmentUseCase;
        $this->parserInterface = $parserInterface;
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentRateUseCase = $assessmentRateUseCase;
        $this->viewAsAssessorUseCase = $viewAsAssessorUseCase;
        $this->middleware('type:assessment_manager');
    }


    public function index(Request $request)
    {
        $this->setFilters();
        $filter = $request->query->all();

        $assessments = $this->assessmentRepo->listAssessmentManagerAssessments($filter);
        
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude(
            $assessments,
            '',
            new AssessmentTransformer(),
            ResourceTypesEnums::ASSESSMENT,
            $meta
        );
    }

    public function createAssessment(CreateAssessmentRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->createAssessmentUseCase->createAssessment($data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['assessment'],
                'assessors,assessees,assessmentResultViewerTypes',
                new AssessmentTransformer(),
                ResourceTypesEnums::ASSESSMENT,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    /**
     * Update Assessment
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param $assessment
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(CreateAssessmentRequest $request, $assessmentId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->updateAssessmentUseCase->editAssessment($assessmentId, $data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['assessment'],
                'assessors,assessees,assessmentResultViewerTypes',
                new AssessmentTransformer(),
                ResourceTypesEnums::ASSESSMENT,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    /**
     * Make Assessment published
     * @param Assessment $assessment
     * @return \Illuminate\Http\Response
     */
    public function publish(Assessment $assessment)
    {
        if ($assessment->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $useCase = $this->updateAssessmentUseCase->publishAssessment($assessment);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('api.Published Successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }

    /**
     * @param Assessment $assessment
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublish(Assessment $assessment)
    {
        if ($assessment->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $useCase = $this->updateAssessmentUseCase->unpublishAssessment($assessment);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('api.unPublished Successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function show(Assessment $assessment)
    {
        return $this->transformDataModInclude(
            $assessment,
            'assessors,
            assessees,
            assessmentResultViewerTypes,
            assessmentPointsRates',
            new AssessmentTransformer(),
            ResourceTypesEnums::ASSESSMENT
        );
    }

    public function delete(Assessment $assessment)
    {
        if ($assessment->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $this->updateAssessmentUseCase->delete($assessment);

        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

    /**
     * Store points rate of assessment
     * @param App\OurEdu\Assessments\AssessmentManager\Requests\AssessmentPointsRateRequest $request
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRates(AssessmentPointsRateRequest $request, Assessment $assessment)
    {
        $serializedContent = $this->parserInterface->deserialize($request->getContent());
        $data = $serializedContent->getData();
        $rates = $data->rates;

        $useCase = $this->assessmentRateUseCase->createPointRates($assessment, $rates);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['assessmentPointsRates'],
                '',
                new AssessmentPointsRateTransformer(),
                ResourceTypesEnums::ASSESSMENT_POINTS_RATE,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    /**
     * List assessment points rates
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssessmentPointsRates(Assessment $assessment)
    {
        $rates = $this->assessmentRateUseCase->getAssessmentRates($assessment);

        return $this->transformDataModInclude(
            $rates,
            '',
            new AssessmentPointsRateTransformer(),
            ResourceTypesEnums::ASSESSMENT_POINTS_RATE
        );
    }
    public function cloneAssessment(CloneAssessmentRequest $request, Assessment $assessment)
    {
        if ($assessment->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->cloneAssessmentUseCase->cloneAssessment($assessment, $data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['assessment'],
                'assessors,assessees,assessmentResultViewerTypes',
                new AssessmentTransformer(),
                ResourceTypesEnums::ASSESSMENT,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function preview(Assessment $assessment)
    {
        $page = request('page') ?? 1;
        $usecase = $this->viewAsAssessorUseCase->nextOrBackQuestion($assessment->id, $page);

        if ($usecase['status'] == 200) {
            $questions = $usecase['questions'];
            $assessment = $usecase['assessment'];

            return $this->transformDataModInclude($questions, 'questions', new QuestionViewAsAssessorTransformer($assessment), ResourceTypesEnums::ASSESSMENT_QUESTION_DATA);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function setFilters()
    {

        $this->filters[] = [
            'name' => 'assessor_type',
            'type' => 'select',
            'data' => UserEnums::assessmentUserTypes(),
            'trans' => false,
            'value' => request()->get('assessor_type'),
        ];

        $this->filters[] = [
            'name' => 'assessee_type',
            'type' => 'select',
            'data' => UserEnums::assessmentUserTypes(),
            'trans' => false,
            'value' => request()->get('assessee_type'),
        ];
        
        $this->filters[] = [
            'name' => 'from_date',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('from_date'),
        ];

        $this->filters[] = [
            'name' => 'to_date',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('to_date'),
        ];
    }
}
