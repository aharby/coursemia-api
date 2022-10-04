<?php

namespace App\OurEdu\Assessments\AssessmentResultViewer\Controllers\Web;

use App\OurEdu\Assessments\AssessmentResultViewer\Exports\Web\AssessmentReportExport;
use App\OurEdu\Assessments\AssessmentResultViewer\Exports\Web\AssessorAssesseeAssessmentsDetailsReportExport;
use App\OurEdu\Assessments\AssessmentResultViewer\Exports\Web\AssessorAssessesReportExport;
use App\OurEdu\Assessments\AssessmentResultViewer\Exports\Web\AssessorsReportExport;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssessmentReportsController extends BaseApiController
{
    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;

    /**
     * AssessmentController constructor.
     * @param AssessmentUsersRepositoryInterface $assessmentUsersRepository
     */
    public function __construct(AssessmentUsersRepositoryInterface $assessmentUsersRepository)
    {
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    /**
     * List all assessments for the viewer
     * @return View|Factory
     */
    public function index(Request $request)
    {
        $filter = $request->query->all();
        $data["assessments"] = $this->assessmentUsersRepository->viewerAssessments(Auth::user(),true,$filter);

        $data['userTypes'] = UserEnums::assessmentUserTypes();

        $data["page_title"] = trans("navigation.assessments_list");

        return view("school_supervisor.reports.assessments.index", $data);
    }

    /**
     * List all assessments for the Export
     * @return BinaryFileResponse;

     */
    public function indexExport(Request $request)
    {
        $filter = $request->query->all();
        $assessments = $this->assessmentUsersRepository->viewerAssessments(Auth::user(), false,$filter);

        return Excel::download(new AssessmentReportExport($assessments), trans("navigation.assessments").".xls");
    }

    /**
     * List all assessment assessors for the viewer
     * @param Assessment $assessment
     * @return View|Factory
     */
    public function viewAssessmentAssessors(Assessment $assessment)
    {
        $data["assessmentAssessors"] = $this->assessmentUsersRepository->getAssessmentAssessors($assessment);
        $data["assessment"] = $assessment;
        $data["page_title"] = trans("navigation.assessment_assessors");

        return view("school_supervisor.reports.assessments.assessors", $data);
    }

    /**
     * List all assessment assessors for the Export
     * @param Assessment $assessment
     * @return BinaryFileResponse
     */
    public function viewAssessmentAssessorsExport(Assessment $assessment)
    {
        $assessmentAssessors = $this->assessmentUsersRepository->getAssessmentAssessors($assessment, false);

        return Excel::download(new AssessorsReportExport($assessment, $assessmentAssessors), trans("navigation.assessment_assessors").".xls");
    }

    /**
     * List all assessment assessees for the viewer
     * @param Assessment $assessment
     * @param User $assessor
     * @return Factory|View
     */
    public function viewAssessmentAssessees(Assessment $assessment, User $assessor)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessor->id);
        $data["assessment"] = $assessment;
        $data['assessor'] = $assessor;
        $data["rates"] = $assessment->rates;
        $data["page_title"] = trans("navigation.assessment_assessees");

        return view("school_supervisor.reports.assessments.assessees", $data);
    }

    /**
     * List all assessment assessees for the Export
     * @param Assessment $assessment
     * @param User $assessor
     * @return BinaryFileResponse
     */
    public function viewAssessmentAssesseesExport(Assessment $assessment, User $assessor)
    {
        $assesses = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessor->id, false);

        return Excel::download(new AssessorAssessesReportExport($assessment, $assesses), trans("navigation.assessment_assessees").".xls");
    }

    /**
     * List all assessment assessees for the viewer
     * @param Assessment $assessment
     * @param User $assessor
     * @return Factory|View
     */
    public function viewAssesseeAssessments(Assessment $assessment, User $assessor, User $assessee)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee);
        $data["assessment"] = $assessment;
        $data['assessor'] = $assessor;
        $data['assessee'] = $assessee;
        $data["rates"] = $assessment->rates;
        $data["page_title"] = trans("navigation.assessment_assessees");

        return view("school_supervisor.reports.assessments.assesseesDetails", $data);
    }

    /**
     * List all assessment assessees for the viewer
     * @param Assessment $assessment
     * @param User $assessor
     * @return BinaryFileResponse
     */
    public function viewAssesseeAssessmentsExport(Assessment $assessment, User $assessor, User $assessee)
    {
        $assessmentUsers = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee, false);

        return Excel::download(new AssessorAssesseeAssessmentsDetailsReportExport($assessmentUsers, $assessment), trans("navigation.assessment_assessees").".xls");
    }
}
