<?php

namespace App\OurEdu\Assessments\AssessmentManager\Controllers\Api;

use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentQuestionTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessorAssesseesDetailsReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentAssessorReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessorAssesseesReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AtQuestionLevelReport\AssessmentQuestionScoreTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AtQuestionLevelReport\AtQuestionAssessmentReportTransformer;
use App\OurEdu\Assessments\Exports\AssessmentsAtQuestionReportExport;
use App\OurEdu\Assessments\Exports\AssessorAssesseeDetailsReportExport;
use App\OurEdu\Assessments\Exports\QuestionReportExport;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Exports\AssessmentAssessorsReportExport;
use App\OurEdu\Assessments\Exports\AssessmentReportExport;
use App\OurEdu\Assessments\Exports\AssessorAssesseeReportExport;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentAverageQuestionReport\AssessmentTransformer;
use App\OurEdu\Assessments\Exports\AssessmentAverageQuestionReportExport;

class AssessmentReportsController extends BaseApiController
{
    private AssessmentUsersRepositoryInterface $assessmentUsersRepository;
    private AssessmentRepositoryInterface $assessmentRepo;
    private array $filters = [];
    /**
     * AssessmentReportsController constructor.
     * @param AssessmentUsersRepositoryInterface $assessmentUsersRepository
     * @param AssessmentRepositoryInterface $assessmentRepo
     */


    /**
     * AssessmentReportsController constructor.
     */
    public function __construct(
        AssessmentUsersRepositoryInterface $assessmentUsersRepository,
        AssessmentRepositoryInterface $assessmentRepo
    ) {
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    public function getAssessmentReport(Request $request)
    {
        $this->setFilters();
        $filter = $request->query->all();
        $data = $this->assessmentRepo->listAssessmentManagerAssessmentsReport(true, $filter);
        $params = ['user' => auth()->user()];
        $include = '';
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude(
            $data,
            $include,
            new AssessmentReportTransformer($params),
            ResourceTypesEnums::ASSESSMENT_REPORT,
            $meta
        );
    }

    public function exportAssessmentReport(Request $request)
    {
        $filter = $request->query->all();
        $data = $this->assessmentRepo->listAssessmentManagerAssessmentsReport(false, $filter);
        return Excel::download(new AssessmentReportExport($data), "assessment-report.xls");
    }

    public function getAssessmentAssessorReport(Assessment $assessment)
    {
        $assessors = $this->assessmentUsersRepository->getAssessmentAssessors($assessment);
        $include = '';
        return $this->transformDataModInclude(
            $assessors,
            $include,
            new AssessmentAssessorReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSOR
        );
    }

    public function exportAssessmentAssessorsReport(Assessment $assessment)
    {
        $data = $this->assessmentUsersRepository->getAssessmentAssessors($assessment, false);
        return Excel::download(new AssessmentAssessorsReportExport($data), "assessment-assessors-report.xls");
    }

    public function getAssessorAssesseesReport(Assessment $assessment, $assessorId)
    {
        $data = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessorId);
        $include = '';
        return $this->transformDataModInclude(
            $data,
            $include,
            new AssessorAssesseesReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSEE
        );
    }

    public function exportAssessorAssesseesReport(Assessment $assessment, $assessorId)
    {
        $data = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment, $assessorId, false);
        return Excel::download(new AssessorAssesseeReportExport($data), "assessor-assessee-report.xls");
    }


    public function getAssessorAssesseesAssessmentsReport(Assessment $assessment, User $assessor, User $assessee)
    {
        $data = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee);
        $include = '';
        return $this->transformDataModInclude(
            $data,
            $include,
            new AssessorAssesseesDetailsReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSEE
        );
    }

    public function exportAssessorAssesseesAssessmentsReport(Assessment $assessment, User $assessor, User $assessee)
    {
        $data = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor(
            $assessment,
            $assessor,
            $assessee,
            false
        );

        return Excel::download(new AssessorAssesseeDetailsReportExport($data), "assessor-assessee-report.xls");
    }

    public function atQuestionReport(Request $request)
    {
        $filter = $request->query->all();
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters),
            'school_name' => auth()->user()->school->name
        ];
        $data = $this->assessmentRepo->assessmentsWithFilter($filter);
        $params = ["hasBranch" => isset($filter['branch'])];
        $include = '';

        return $this->transformDataModInclude(
            $data,
            $include,
            new AtQuestionAssessmentReportTransformer($params),
            ResourceTypesEnums::ASSESSMENT_REPORT,
            $meta
        );
    }

    public function atQuestionReportExport(Request $request)
    {
        $filter = $request->query->all();
        $school = Auth::user()->school;

        $branch = SchoolAccountBranch::query()->find($filter['branch'] ?? null);
        $branchName = $branch ? "_" . $branch->name : "";

        $fileName = ($school->name ?? "assessments-at-question-report") . $branchName . ".xls";
        $data = $this->assessmentRepo->assessmentsWithFilter($filter, false);
        $params = ["hasBranch" => isset($filter['branch'])];

        $file = Excel::download(new AssessmentsAtQuestionReportExport($data, $params), $fileName);
        $file->headers->set("file-name", $fileName);

        return $file;
    }

    public function QuestionReport(Request $request, Assessment $assessment)
    {
        $filter = $request->query->all();

        $assessmentQuestions = $this->assessmentRepo->getAssessmentQuestionsWithFilter($assessment, $filter);
        $params = ["hasBranch" => isset($filter['branch'])];
        $include = '';
        $meta['school_name'] = auth()->user()->school->name;
        return $this->transformDataModInclude(
            $assessmentQuestions,
            $include,
            new AssessmentQuestionScoreTransformer($assessment, $params),
            ResourceTypesEnums::ASSESSMENT_QUESTION,
            $meta
        );
    }

    public function QuestionReportExport(Request $request, Assessment $assessment)
    {
        $filter = $request->query->all();
        $branch = SchoolAccountBranch::query()->find($filter['branch'] ?? null);
        $branchName = $branch ? "_" . $branch->name : "";
        $fileName = $assessment->title . $branchName . ".xls";

        $assessmentQuestions = $this->assessmentRepo->getAssessmentQuestionsWithFilter($assessment, $filter, false);
        $params = ["hasBranch" => isset($filter['branch'])];
        $file = Excel::download(
            new QuestionReportExport($assessmentQuestions, $params),
            preg_replace('/\\\\|\//i', '', $fileName)
        );
        $file->headers->set("file-name", $fileName);

        return $file;
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


    public function assessmentWithAverageQuestions(Request $request)
    {
        $this->setFilters();
        $filter = $request->query->all();

        $meta = [
            'filters' => formatFiltersForApi($this->filters),
        ];
        $data = $this->assessmentRepo->getAssessmentWithQuestion(true, $filter);
        $params = ['user' => auth()->user()];
        $include = '';
        return $this->transformDataModInclude(
            $data,
            $include,
            new AssessmentTransformer($params),
            ResourceTypesEnums::ASSESSMENT_AVERAGE_QUESTION_REPORT,
            $meta
        );
    }

    public function assessmentWithAverageQuestionsExport(Request $request)
    {
        $filter = $request->query->all();
        $data = $this->assessmentRepo->getAssessmentWithQuestion(false, $filter);
        return Excel::download(
            new AssessmentAverageQuestionReportExport($data),
            "assessment-average-questions-report.xls"
        );
    }

    public function assessmentAnswersPercentage(Assessment $assessment)
    {
        $questions = $this->assessmentRepo->questionAnswersPercentage($assessment->id);

        return $this->transformDataModInclude(
            $questions,
            'questions.options.answersCount,questions.rows.answersCount',
            new AssessmentQuestionTransformer($assessment, ['essay_answers' => true]),
            ResourceTypesEnums::ASSESSMENT_QUESTION
        );
    }
}
