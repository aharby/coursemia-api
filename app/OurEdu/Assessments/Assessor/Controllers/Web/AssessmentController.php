<?php

namespace App\OurEdu\Assessments\Assessor\Controllers\Web;

use App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase\StartAssessmentUseCaseInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends BaseApiController
{
    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;

    /**
     * AssessmentController constructor.
     * @param AssessmentUsersRepositoryInterface $assessmentUsersRepository
     */
    public function __construct(
        AssessmentUsersRepositoryInterface $assessmentUsersRepository
    ) {
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    /**
     * List all resources
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data["assessments"] = $this->assessmentUsersRepository->getAssessmentsByAssessor(Auth::user());

        $data["page_title"] = trans("navigation.assessments");

        return view("school_supervisor.assessments.index", $data);
    }

    /**
     * List all assessees of assessment
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @return \Illuminate\View\View
     */
    public function listAssessees(Assessment $assessment)
    {
        $data["assessmentAssessees"] = $this->assessmentUsersRepository->getAssessorAssessees($assessment, auth()->user());
        $data["page_title"] = trans("navigation.assessment_assessees");
        $data["assessmentID"] = $assessment->id;
        $data["assessmentTitle"] = $assessment->title;
        $data['assesseesFinishedAssessment'] = $assessment->assessmentUsers()->where('is_finished',1)->pluck('assessee_id')->toArray();
        return view("school_supervisor.assessments.assessees", $data);
    }
}
