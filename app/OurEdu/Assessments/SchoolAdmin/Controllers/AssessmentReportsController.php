<?php

namespace App\OurEdu\Assessments\SchoolAdmin\Controllers;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\SchoolAdmin\Middleware\SchoolAdminMiddleware;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\OurEdu\Assessments\SchoolAdmin\Exports\AssessmentReportExport;
use App\OurEdu\Assessments\SchoolAdmin\Exports\AssessorsReportExport;
use App\OurEdu\Assessments\SchoolAdmin\Exports\AssessorAssessesReportExport;
use App\OurEdu\Assessments\SchoolAdmin\Exports\AssessorAssesseeAssessmentsDetailsReportExport;

class AssessmentReportsController extends BaseApiController
{
    /**
     * @var AssessmentRepositoryInterface
     */
    private $assessmentRepository;

    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;


    /**
     * AssessmentController constructor.
     * @param AssessmentRepositoryInterface $assessmentRepository
     */
    public function __construct(
        AssessmentRepositoryInterface $assessmentRepository,
        AssessmentUsersRepositoryInterface $assessmentUsersRepository,
    ) {
        $this->assessmentRepository = $assessmentRepository;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
        $this->middleware(SchoolAdminMiddleware::class);
    }



    public function index(Request $request)
    {
        $filter = $request->query->all();
        $data["assessments"] = $this->assessmentRepository->listSchoolAdminAssessmentsReport(true, $filter);
        $data['userTypes'] = UserEnums::assessmentUserTypes();

        $data["page_title"] = trans("navigation.assessments");

        return view("school_admin.reports.assessments.index", $data);
    }

    public function indexExport(Request $request)
    {
        $filter = $request->query->all();
        $data = $this->assessmentRepository->listSchoolAdminAssessmentsReport(false, $filter);
        return Excel::download(new AssessmentReportExport($data), trans("navigation.assessments").'-'. auth()->user()->schoolAdmin->currentSchool->name . ".xls");
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
        return view("school_admin.reports.assessments.assessors", $data);
    }

    public function viewAssessmentAssessorsExport(Assessment $assessment)
    {
        $assessmentAssessors = $this->assessmentUsersRepository->getAssessmentAssessors($assessment, false);

        return Excel::download(new AssessorsReportExport($assessment, $assessmentAssessors), trans("navigation.assessment_assessors") .'-'. auth()->user()->schoolAdmin->currentSchool->name . ".xls");
    }

    public function viewAssessmentAssessees(Assessment $assessment, User $assessor)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessor->id);
        $data["assessment"] = $assessment;
        $data['assessor'] = $assessor;
        $data["rates"] = $assessment->rates;
        $data["page_title"] = trans("navigation.assessment_assessees");
        return view("school_admin.reports.assessments.assessees", $data);
    }

    public function viewAssessmentAssesseesExport(Assessment $assessment, User $assessor)
    {
        $assesses = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessor->id, false);

        return Excel::download(new AssessorAssessesReportExport($assessment, $assesses), trans("navigation.assessment_assessees").'-'. auth()->user()->schoolAdmin->currentSchool->name . ".xls");
    }

    public function viewAssesseeAssessments(Assessment $assessment, User $assessor, User $assessee)
    {
        $data["assessmentUsers"] = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee);
        $data["assessment"] = $assessment;
        $data['assessor'] = $assessor;
        $data['assessee'] = $assessee;
        $data["rates"] = $assessment->rates;
        $data["page_title"] = trans("navigation.assessment_assessees");

        return view("school_admin.reports.assessments.assesseesDetails", $data);
    }

    public function viewAssesseeAssessmentsExport(Assessment $assessment, User $assessor, User $assessee)
    {
        $assessmentUsers = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee, false);
        return Excel::download(new AssessorAssesseeAssessmentsDetailsReportExport($assessmentUsers, $assessment), trans("navigation.assessment_assessees") .'-'. auth()->user()->schoolAdmin->currentSchool->name . ".xls");
    }
}
