<?php

namespace App\OurEdu\Assessments\Assessee\Controllers\Web;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
    public function listAssessments(Request $request)
    {
        $filter = $request->query->all();
        
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssessmentsByAssessee(Auth::user(),$filter);
        $data["page_title"] = trans("navigation.my_assessments");

        $data['assessorTypes'] = UserEnums::assessmentUserTypes();

        return view("school_supervisor.assessments.assesseeAssessments", $data);
    }

    /**
     * List all assessees of assessment
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @return \Illuminate\View\View
     */
    public function listAssessors(Assessment $assessment)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getGroupedAssessAssessors($assessment->id, auth()->user()->id );
        $data["page_title"] = $assessment->title;

        return view("school_supervisor.assessments.assesseeAssessors", $data);
    }

    public function getAssessorsAnswersAttempts(Assessment $assessment, User $assessee,User $assessor)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor( $assessment,  $assessor,  $assessee ,true);

        $data["page_title"] = $assessment->title;
        $data["assessment"] =$assessment;

        return view("school_supervisor.reports.assessments.attemptsList", $data);
    }
}
