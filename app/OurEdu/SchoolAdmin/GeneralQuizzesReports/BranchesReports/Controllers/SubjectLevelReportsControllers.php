<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\BranchesReports\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReport\Exports\InstructorLevelReportExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports\SubjectLevelReportExport;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SubjectLevelReportsControllers extends BaseController
{
    private $userSchool;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * @var SubjectRepositoryInterface
     */
    private $subjectRepository;

    /**
     * ReportsController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function subjectLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs =  $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }
        $data["gradeClasses"] = [];
        if ($branch) {
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();
        }

        $subjects = [];
        $sessions = [];

        if ($request->filled("gradeClass")) {
            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);
        }

        $data["branches"] =$user->schoolAdmin->currentSchool->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.subject level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.branch_reports.subject_level.branch_reports_subject_level", $data);
    }

    public function subjectLevelExport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs =  $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new SubjectLevelReportExport($generalQuizzes), "subject level reports.xls");

    }

    public function subjectLevelChart(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }
        $data["gradeClasses"] = [];
        if ($branch) {
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();
        }

        $subjects = [];
        $sessions = [];

        if ($request->filled("gradeClass")) {
            $subjects = Subject::query()
                ->where("grade_class_id", "=", $request->get("gradeClass"))
                ->pluck('name', 'id')
                ->toArray();
        }

        $data["branches"] = $user->schoolAdmin->currentSchool->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.subject level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);
        return view("school_admin.GeneralQuizzesReports.branch_reports.subject_level.branch_reports_subject_level_charts", $data);
    }
}
