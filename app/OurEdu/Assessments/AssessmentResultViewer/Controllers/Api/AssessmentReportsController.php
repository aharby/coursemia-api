<?php


namespace App\OurEdu\Assessments\AssessmentResultViewer\Controllers\Api;

use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentAssessorReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessorAssesseesDetailsReportTransformer;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessorAssesseesReportTransformer;
use App\OurEdu\Assessments\Exports\AssessmentAssessorsReportExport;
use App\OurEdu\Assessments\Exports\AssessmentReportExport;
use App\OurEdu\Assessments\Exports\AssessorAssesseeDetailsReportExport;
use App\OurEdu\Assessments\Exports\AssessorAssesseeReportExport;
use App\OurEdu\Assessments\AssessmentResultViewer\Middleware\AssessmentResultViewMiddleware;
use App\OurEdu\Assessments\AssessmentResultViewer\Transformers\AssessorAnswerTransformer;
use App\OurEdu\Assessments\Assessor\Transformers\AssessmentQuestionTransformer;
use App\OurEdu\Assessments\Assessor\UseCases\NextAndBack\AssessmentNextBackUseCase;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentReportsController extends BaseApiController
{
    /**
     * @var AssessmentUsersRepositoryInterface
     */
    private $assessmentUsersRepository;

    private $filters = [];
     /**
     * @var AssessmentRepositoryInterface
     */
    private $assessmentRepo;

    /**
     * @var AssessmentNextBackUseCase
     */
    private $assessmentNextBackUseCase;

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
        AssessmentRepositoryInterface $assessmentRepo,
        AssessmentNextBackUseCase $useCase
    ){
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
        $this->assessmentNextBackUseCase = $useCase;
        $this->middleware(AssessmentResultViewMiddleware::class)->only('getAssessmentAssessorReport','getAssessorAssesseesReport');
    }

    public function getAssessmentReport(Request $request){
        $this->setFilters();
        $filter = $request->query->all();
        $data = $this->assessmentRepo->listAssessmentReportForResultViewers(true,$filter);
        $params = ['user'=> auth()->user()];
        $include =  '';
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude($data, $include, new AssessmentReportTransformer($params), ResourceTypesEnums::ASSESSMENT_REPORT,$meta);
    }


    public function exportAssessmentReport(Request $request){
        $filter = $request->query->all();
        $data = $this->assessmentRepo->listAssessmentReportForResultViewers(false,$filter);
        return Excel::download(new AssessmentReportExport($data), "assessment-report.xls");
    }



    public function getAssessmentAssessorReport(Assessment $assessment){
        $assessors = $this->assessmentUsersRepository->getAssessmentAssessors($assessment);
        $include =  '';
        return $this->transformDataModInclude(
            $assessors, $include, new AssessmentAssessorReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSOR
        );
    }

    public function exportAssessmentAssessorsReport(Assessment $assessment){
        $data = $this->assessmentUsersRepository->getAssessmentAssessors($assessment,false);
        return Excel::download(new AssessmentAssessorsReportExport($data), "assessment-assessors-report.xls");
    }

    public function getAssessorAssesseesReport(Assessment $assessment,$assessorId){
        $data = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment,$assessorId);
        $include =  '';
        return $this->transformDataModInclude(
        $data, $include, new AssessorAssesseesReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSEE
        );
    }

    public function exportAssessorAssesseesReport(Assessment $assessment,$assessorId){
        $data = $this->assessmentUsersRepository->getAssesseeByAssessorId($assessment,$assessorId,false);
        return Excel::download(new AssessorAssesseeReportExport($data), "assessor-assessee-report.xls");
    }


    public function getAssessorAssesseesAssessmentsReport(Assessment $assessment,User $assessor, User $assessee){
        $data = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee);
        $include =  '';
        return $this->transformDataModInclude(
            $data, $include, new AssessorAssesseesDetailsReportTransformer(),
            ResourceTypesEnums::ASSESSMENT_ASSESSEE
        );
    }

    public function exportAssessorAssesseesAssessmentsReport(Assessment $assessment,User $assessor, User $assessee){
        $data = $this->assessmentUsersRepository->getAssesseeDetailsByAssessor($assessment, $assessor, $assessee, false);

        return Excel::download(new AssessorAssesseeDetailsReportExport($data), "assessor-assessee-report.xls");
    }

    public function getAssessorAnswer(AssessmentUser $assessmentUser)
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 1);
        $assessment = $assessmentUser->assessment;

        $usecase = $this->assessmentNextBackUseCase->getQuestions($assessment, $assessmentUser, $page, $perPage);

        if ($usecase['status'] == 200) {
            $params['assessmentUser'] = $assessmentUser;

            $meta = [
                'id' => $assessmentUser->id,
                'assessee_name' => (string)$assessmentUser->assessee->name,
                'total_points' => (string)"{$assessmentUser->score} / {$assessmentUser->total_mark}",
            ];

            return $this->transformDataModInclude(
                $usecase['questions'],
                '',
                new AssessmentQuestionTransformer($usecase['assessment'], $params),
                ResourceTypesEnums::ASSESSMENT_QUESTION,
                $meta
            );
        } else{
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


