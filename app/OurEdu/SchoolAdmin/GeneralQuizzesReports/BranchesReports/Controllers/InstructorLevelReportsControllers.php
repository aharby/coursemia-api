<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\BranchesReports\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReport\Exports\InstructorLevelReportExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports\SubjectLevelReportExport;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class InstructorLevelReportsControllers extends BaseController
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

    public function instructorLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs =   $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

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

        $classrooms = [];
        $instructors = [];
        $subjects = [];
        $sessions = [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");

            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);
        }

        if ($request->filled("classroom")) {
            $instructorsData = User::query()
                ->whereHas("schoolInstructorSessions" , function (Builder $sessions) use ($request) {
                    $sessions->where("classroom_id", "=", $request->get("classroom"));
                })
                ->get();

            foreach ($instructorsData as $instructor) {
                $instructors[$instructor->id] = $instructor->first_name . " " . $instructor->last_name;
            }
        }

        if ($request->filled("instructor")) {
            $instructor = User::query()->find($request->get("instructor"));
            $subjects = $instructor->schoolInstructorSubjects()->pluck('name', 'id')->toArray();
        }

        $data["branches"] = $user->schoolAdmin->currentSchool->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.instructor level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.branch_reports.instructor_level.branch_reports_instructor_level", $data);
    }
    public function instructorLevelChart(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs =$user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

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

        $classrooms = [];
        $instructors = [];
        $subjects = [];
        $sessions = [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
        }

        if ($request->filled("classroom")) {
            $instructorsData = User::query()
                ->whereHas("schoolInstructorSessions" , function (Builder $sessions) use ($request) {
                    $sessions->where("classroom_id", "=", $request->get("classroom"));
                })
                ->get();

            foreach ($instructorsData as $instructor) {
                $instructors[$instructor->id] = $instructor->first_name . " " . $instructor->last_name;
            }
        }

        if ($request->filled("instructor")) {
            $instructor = User::query()->find($request->get("instructor"));
            $subjects = $instructor->schoolInstructorSubjects()->pluck('name', 'id')->toArray();
        }

        $data["branches"] = $user->schoolAdmin->currentSchool->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.instructor level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.branch_reports.instructor_level.branch_reports_instructor_level_charts", $data);
    }
    public function instructorLevelExport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs =   $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new InstructorLevelReportExport($generalQuizzes),"instructor level report.xls");
    }
}
