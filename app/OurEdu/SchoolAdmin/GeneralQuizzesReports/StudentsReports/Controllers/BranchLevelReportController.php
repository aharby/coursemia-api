<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\StudentsReports\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\BranchLevelReportExport;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories\GeneralQuizRepository;
use App\OurEdu\Subjects\Models\Subject;
use Maatwebsite\Excel\Facades\Excel;

class BranchLevelReportController extends BaseController
{

    public function __construct()
    {
        $this->generalQuizRepository = new GeneralQuizRepository();
    }

    
    public function GeneralQuizBranchLevelReport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branches = $user->schoolAdmin->currentSchool->branches();
        $branchesIDs = $branches->pluck('id')->toArray();

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

        if ($request->filled("gradeClass") && $branch) {
            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->getGradeBranchSubjectsPluck($branch, $gradeClass);
        }

        $data["branches"] = $branches->pluck('name', 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.Branch Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.branch-level.index", $data);
    }


    private  function getGradeBranchSubjectsPluck(SchoolAccountBranch $branch, GradeClass $gradeClass)
    {
        $branchEducationalSystemsIDs = $branch->branchEducationalSystem()->pluck("educational_system_id")->toArray();
        $branchEducationalSystemsAcademicYears = $branch->branchEducationalSystem()->pluck("academic_year_id")->toArray();
        $branchEducationalSystemsEdcayionalTerms = $branch->branchEducationalSystem()->pluck("educational_term_id")->toArray();

        $educationalSystem = EducationalSystem::query()->whereIn("id", $branchEducationalSystemsIDs)->pluck("id")->toArray();

        $subjects = Subject::query()
            ->whereIn('educational_system_id', $educationalSystem)
            ->whereIn('academical_years_id', $branchEducationalSystemsAcademicYears)
            ->whereIn('educational_term_id', $branchEducationalSystemsEdcayionalTerms)
            ->where("grade_class_id", "=", $gradeClass->id)
            ->pluck("name", "id");

        return $subjects;
    }

    public function generalQuizBranchLevelExport(Request $request)
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

        $data['rows'] = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new BranchLevelReportExport($data['rows']), "branch level report.xls");
    }


    public function generalQuizBranchLevelStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score_percentage', 'student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.Students");
        return view("school_admin.GeneralQuizzesReports.branch-level.students", $data);
    }
}
