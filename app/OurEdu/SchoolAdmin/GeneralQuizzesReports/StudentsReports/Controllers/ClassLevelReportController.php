<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\StudentsReports\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\SubjectLevelReportExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\SectionPercentageReportExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\SkillPercentageLevelReportExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\InstructorLevelReportExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\TotalPercentageReportExport;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\BranchLevelReportExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\ClassLevelReportExport;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories\GeneralQuizRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;

class ClassLevelReportController extends BaseController
{

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
    public function __construct()
    {
        $this->generalQuizRepository = new GeneralQuizRepository();
        // $this->subjectRepository = $subjectRepository;
    }

    public function classLevel(Request $request)
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

        $subjects = $classrooms =  [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
        }

        $data["branches"] = $branches->pluck('name', 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Class Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.classroom-level.index", $data);
    }

    public function classLevelExport(Request $request)
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
        return Excel::download(new ClassLevelReportExport($data['rows']), "class level report.xls");
    }


    public function classLevelChart(Request $request)
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

        $subjects = $classrooms =  [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
        }

        $data["branches"] = $branches->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Class Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.GeneralQuizzesReports.classroom-level.index_charts", $data);
    }
//     public function GeneralQuizBranchLevelReport(Request $request)
    // {
        // $requestData = $request->all();
// 
        // $user = Auth::user();
        // $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();
// 
        // $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();
// 
        // if ($branch and !in_array($branch->id, $branchesIDs)) {
            // return unauthorize();
        // }
// 
        // if (!$branch) {
            // $requestData['branch_id'] = $branchesIDs;
        // }
        // $data["gradeClasses"] = [];
        // if ($branch) {
            // $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();
        // }
// 
        // $subjects = [];
// 
        // if ($request->filled("gradeClass") && $branch) {
            // $gradeClass = GradeClass::query()
                // ->where("id", "=", $request->get('gradeClass'))
                // ->first();
// 
            // $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);
        // }
// 
        // $data["branches"] = $user->schoolAccount->branches()->pluck('name', 'id')->toArray();
        // $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        // $data["subjects"] = $subjects;
        // $data["page_title"] = trans("navigation.Branch Level Report");
        // $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);
// 
        // return view("school_account_manager.generalQuizReports.branch-level.index", $data);
    // }
// 
    // public function generalQuizBranchLevelExport(Request $request)
    // {
        // $requestData = $request->all();
// 
        // $user = Auth::user();
        // $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();
// 
        // $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();
// 
        // if ($branch and !in_array($branch->id, $branchesIDs)) {
            // return unauthorize();
        // }
// 
        // if (!$branch) {
            // $requestData['branch_id'] = $branchesIDs;
        // }
// 
        // $data['rows'] = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);
// 
        // return Excel::download(new BranchLevelReportExport($data['rows']), "branch level report.xls");
    // }
// 
// 
    // public function generalQuizBranchLevelStudents(GeneralQuiz $generalQuiz)
    // {
        // $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score_percentage', 'student_id')->toArray();
        // $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        // $data['generalQuiz'] = $generalQuiz;
        // $data["page_title"] = trans("navigation.Students");
        // return view("school_account_manager.generalQuizReports.branch-level.students", $data);
    // }
// 
// 
    // public function skillPercentageLevelReport(Request $request)
    // {
        // $requestData = $request->all();
// 
        // $user = Auth::user();
        // $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();
// 
        // $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();
// 
        // if ($branch and !in_array($branch->id, $branchesIDs)) {
            // return unauthorize();
        // }
// 
        // if (!$branch) {
            // $requestData['branch_id'] = $branchesIDs;
        // }
        // $data["gradeClasses"] = [];
        // if ($branch) {
            // $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();
        // }
// 
        // $subjects = $classrooms = [];
// 
        // if ($request->filled("gradeClass")) {
            // $gradeClass = GradeClass::query()
                // ->where("id", "=", $request->get('gradeClass'))
                // ->first();
// 
            // $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);
// 
            // $classrooms = $branch
                // ->classrooms()
                // ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    // $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                // })->pluck("name", "id");
        // }
// 
// 
        // $data["branches"] = $user->schoolAccount->branches()->pluck('name', 'id')->toArray();
        // $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        // $data["subjects"] = $subjects;
        // $data["classrooms"] = $classrooms;
        // $data["page_title"] = trans("navigation.Skill Level Report");
        // $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);
        // return view("school_account_manager.generalQuizReports.skill-level.index", $data);
    // }
// 
    // public function skillPercentageLevelReportExport(Request $request)
    // {
        // $requestData = $request->all();
// 
        // $user = Auth::user();
        // $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();
// 
        // $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();
// 
        // if ($branch and !in_array($branch->id, $branchesIDs)) {
            // return unauthorize();
        // }
// 
        // if (!$branch) {
            // $requestData['branch_id'] = $branchesIDs;
        // }
// 
        // $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);
// 
        // return Excel::download(new SkillPercentageLevelReportExport($generalQuizzes), "skill percentage report.xls");
    // }
// 
    // public function sectionPercentageReport(GeneralQuiz $generalQuiz)
    // {
        // $sections = GeneralQuizStudentAnswer::where('general_quiz_id', $generalQuiz->id)
            // ->with([
                // 'section' => function ($query) {
                    // $query->select('id', 'title');
                // }
            // ])->select(
                // 'student_id',
                // 'subject_format_subject_id',
                // DB::raw('SUM(score) as total_score'),
            // )
            // ->groupBy('subject_format_subject_id', 'student_id')
            // ->get()->groupBy('section.title');
// 
        // $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz', function ($query) use ($generalQuiz) {
            // $query->where('general_quiz_id', $generalQuiz->id);
        // })->get()->groupBy('section.title');
        // $data['sectionGrades'] = $sectionGrades;
        // $data['sections'] = $sections;
        // $data['generalQuiz'] = $generalQuiz;
        // $data["page_title"] = trans("navigation.Skill Level Report");
        // return view("school_account_manager.generalQuizReports.skill-level.skills", $data);
    // }
// 
    // public function sectionPercentageReportExport(GeneralQuiz $generalQuiz)
    // {
        // $sections = GeneralQuizStudentAnswer::where('general_quiz_id', $generalQuiz->id)
            // ->with([
                // 'section' => function ($query) {
                    // $query->select('id', 'title');
                // }
            // ])->select(
                // 'student_id',
                // 'subject_format_subject_id',
                // DB::raw('SUM(score) as total_score'),
            // )
            // ->groupBy('subject_format_subject_id', 'student_id')
            // ->get()->groupBy('section.title');
// 
        // $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz', function ($query) use ($generalQuiz) {
            // $query->where('general_quiz_id', $generalQuiz->id);
        // })->get()->groupBy('section.title');
// 
        // return Excel::download(new SectionPercentageReportExport($sections, $generalQuiz, $sectionGrades), "section percentage.xls");
    // }
}
