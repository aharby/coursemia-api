<?php
namespace App\OurEdu\GeneralQuizzesReports\SchoolManager\Controllers;

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
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends BaseController
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
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function totalPercentagesReport()
    {
        $user = Auth::user();
        $schoolQuizzes = GeneralQuiz::query()
            ->where("school_account_id", "=", $user->schoolAccount->id)
            ->whereNotNull("published_at")
            ->where("is_active", "=", 1)
            ->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, SUM(attend_students) as attend_students")
            ->first();

        $schoolStudents = Student::query()
            ->whereHas("classroom.branch", function (Builder $query) use ($user) {
                $query->where("school_account_id", "=", $user->schoolAccount->id);
            })->count();

        $percentage_average_scores = ($schoolQuizzes->attend_students > 0 and $schoolQuizzes->total_marks > 0 ) ? ($schoolQuizzes->students_total_marks / $schoolQuizzes->attend_students/$schoolQuizzes->total_marks)*100 : 0;


        $branches = SchoolAccountBranch::query()
            ->where("school_account_id", "=", $user->schoolAccount->id)
            ->with(['generalQuizzes' => function (HasMany $generalQuiz) {
                $generalQuiz->whereNotNull("published_at");
                $generalQuiz->where("is_active", "=", 1);
                $generalQuiz->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, branch_id, SUM(attend_students) as attend_students")
                    ->groupBy("branch_id");
            }])
            ->get();

        foreach ($branches as $branch) {
            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($branch->generalQuizzes)) {
                $generalQuizCount = $branch->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->attend_students > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks)*100 : 0;
            }

            $branch->general_quizzes_count = $generalQuizCount;
            $branch->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');
        }

        $data["percentage_average_scores"] = number_format($percentage_average_scores, 2, '.', '');
        $data["quizzes_count"] = $schoolQuizzes->count;
        $data["school_students"] = $schoolStudents;
        $data['branches'] = $branches;
        $data['page_title'] = trans('navigation.total percentages report');

        return view("school_account_manager.generalQuizReports.total_percentages_report", $data);
    }

    public function totalPercentagesReportExport()
    {
        $user = Auth::user();

        $branches = SchoolAccountBranch::query()
            ->where("school_account_id", "=", $user->schoolAccount->id)
            ->with(['generalQuizzes' => function (HasMany $generalQuiz) {
                $generalQuiz->whereNotNull("published_at");
                $generalQuiz->where("is_active", "=", 1);
                $generalQuiz->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, branch_id, SUM(attend_students) as attend_students")
                    ->groupBy("branch_id");
            }])
            ->get();

        foreach ($branches as $branch) {
            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($branch->generalQuizzes)) {
                $generalQuizCount = $branch->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->attend_students > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks)*100 : 0;
            }

            $branch->general_quizzes_count = $generalQuizCount;
            $branch->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');
        }

        return Excel::download(new TotalPercentageReportExport($branches), "total percentages Report.xls");
    }


    public function instructorLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.instructor level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_account_manager.generalQuizReports.branch_reports.instructor_level.branch_reports_instructor_level", $data);
    }

    public function instructorLevelExport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

    public function subjectLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.subject level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_account_manager.generalQuizReports.branch_reports.subject_level.branch_reports_subject_level", $data);
    }

    public function subjectLevelExport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

    public function classLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Class Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_account_manager.generalQuizReports.classroom-level.index", $data);
    }

    public function classLevelExport(Request $request)
    {
        $requestData = $request->all();
        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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


    public function GeneralQuizBranchLevelReport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

             $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);
        }

        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.Branch Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_account_manager.generalQuizReports.branch-level.index", $data);
    }

    public function generalQuizBranchLevelExport(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score_percentage','student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.Students");
        return view("school_account_manager.generalQuizReports.branch-level.students", $data);
    }


    public function skillPercentageLevelReport(Request $request){
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

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

        $subjects = $classrooms =[];

        if ($request->filled("gradeClass")) {
            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);

            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })->pluck("name", "id");
        }


        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Skill Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);
        return view("school_account_manager.generalQuizReports.skill-level.index", $data);
    }

    public function skillPercentageLevelReportExport(Request $request){
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new SkillPercentageLevelReportExport($generalQuizzes), "skill percentage report.xls");
    }

    public function sectionPercentageReport(GeneralQuiz $generalQuiz)
    {
        $sections = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
            ->with([
                'section'=>function($query){
                $query->select('id','title');
            }])->select(
                'student_id',
                'subject_format_subject_id',
                DB::raw('SUM(score) as total_score'),
            )
            ->groupBy('subject_format_subject_id','student_id')
            ->get()->groupBy('section.title');

        $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz',function($query)use($generalQuiz){
            $query->where('general_quiz_id',$generalQuiz->id);
        })->get()->groupBy('section.title');
        $data['sectionGrades'] = $sectionGrades;
        $data['sections'] = $sections;
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.Skill Level Report");
        return view("school_account_manager.generalQuizReports.skill-level.skills", $data);
    }

    public function sectionPercentageReportExport(GeneralQuiz $generalQuiz)
    {
        $sections = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
            ->with([
                'section'=>function($query){
                $query->select('id','title');
            }])->select(
                'student_id',
                'subject_format_subject_id',
                DB::raw('SUM(score) as total_score'),
            )
            ->groupBy('subject_format_subject_id','student_id')
            ->get()->groupBy('section.title');

        $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz',function($query)use($generalQuiz){
            $query->where('general_quiz_id',$generalQuiz->id);
        })->get()->groupBy('section.title');

        return Excel::download(new SectionPercentageReportExport($sections, $generalQuiz, $sectionGrades), "section percentage.xls");
    }
}
