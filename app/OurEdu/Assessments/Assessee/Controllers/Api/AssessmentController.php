<?php


namespace App\OurEdu\Assessments\Assessee\Controllers\Api;

use App\OurEdu\Assessments\Assessee\Transformers\AssessmentAssessorTransformer;
use App\OurEdu\Assessments\Assessee\Transformers\AssessmentTransformer;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends BaseApiController
{

    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;
    private $filters = [];

    /**
     * AssessmentController constructor.
     */
    public function __construct(
        AssessmentUsersRepositoryInterface $assessmentUsersRepository
    ) {
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }


    public function listAssessments(Request $request)
    {
        $this->setFilters();
        $filter = $request->query->all();
        $assessments = $this->assessmentUsersRepository->getAssessmentsByAssessee(Auth::user(),$filter);

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModInclude($assessments, "actions", new AssessmentTransformer(), ResourceTypesEnums::ASSESSMENT, $meta);
    }

    public function listAssessors(Assessment $assessment)
    {
        $assessors = $this->assessmentUsersRepository->getAssesseeAssessors($assessment->id, auth()->user()->id);

        return $this->transformDataModInclude($assessors, "", new AssessmentAssessorTransformer($assessment), ResourceTypesEnums::ASSESSMENT_ASSESSOR);
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
