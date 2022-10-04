<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
class ReportsChartsController extends BaseController
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
     * @param SubjectRepositoryInterface $subjectRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function totalPercentagesReport()
    {
        $user = Auth::user();
        $branch = $user->branch()->first();

        $schoolQuizzes = GeneralQuiz::query()
            ->where("branch_id", "=", $branch->id)
            ->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, UM(atStend_students) as attend_students")
            ->first();

        $schoolStudents = Student::query()
            ->whereHas("classroom.branch", function (Builder $query) use ($branch) {
                $query->where("id", "=", $branch->id);
            })->count();

        $percentage_average_scores = ($schoolQuizzes->count > 0 and $schoolQuizzes->total_marks > 0 ) ? ($schoolQuizzes->students_total_marks / $schoolQuizzes->attend_students/$schoolQuizzes->total_marks) : 0;


        $userBranchReportDetails = SchoolAccountBranch::query()
            ->where("id", "=", $branch->id)
            ->with(['generalQuizzes' => function ( $generalQuiz) {
                $generalQuiz->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, SUM(attend_students) as attend_students, branch_id")
                    ->groupBy("branch_id");
            }])
            ->first();

            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($userBranchReportDetails->generalQuizzes)) {
                $generalQuizCount = $userBranchReportDetails->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->count > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks) : 0;
            }

            $userBranchReportDetails->general_quizzes_count = $generalQuizCount;
            $userBranchReportDetails->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');

        $data["percentage_average_scores"] = number_format($percentage_average_scores, 2, '.', '');
        $data["quizzes_count"] = $schoolQuizzes->count;
        $data["school_students"] = $schoolStudents;
        $data['BranchReportDetails'] = $userBranchReportDetails;
        $data['page_title'] = trans('navigation.total percentages report');

        return view("school_supervisor.generalQuizReports.total_percentages_report_charts", $data);
    }


    public function instructorLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $user->branch;
        $requestData['branch_id'] = $branch->id;

        $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();

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
            $subjects = $instructor->schoolInstructorSubjects();

            if(request()->has('gradeClass')){
                $subjects = $subjects->where('grade_class_id',request()->gradeClass);
            }

            if(request()->has('classroom')){
                $subjects = $subjects->whereHas('classroomClasses',function($query){
                    $query->where('classroom_id',request()->classroom);
                });
            }

            $subjects = $subjects->pluck('name', 'id')->toArray();
        }

        $data["branch"] = $branch;
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.instructor level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_supervisor.generalQuizReports.branch_reports.instructor_level.branch_reports_instructor_level_charts", $data);
    }

    public function subjectLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $user->branch;
        $requestData['branch_id'] = $branch->id;

        $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();

        $subjects = [];

        if ($request->filled("gradeClass")) {
            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);

        }

        $data["branch"] = $branch;
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["subjects"] = $subjects;
        $data["page_title"] = trans("navigation.subject level report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_supervisor.generalQuizReports.branch_reports.subject_level.branch_reports_subject_level_charts", $data);
    }

    public function classLevel(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $user->branch;

        $requestData['branch_id'] = $branch->id;

        $data["gradeClasses"] = $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();

         $classrooms =  [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
        }

        $data["branch"] = $branch;
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Class Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_supervisor.generalQuizReports.classroom-level.index_charts", $data);
    }

    public function sectionPercentageReport(GeneralQuiz $generalQuiz)
    {
        $answers = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
            ->with([
                'section'=>function($query){
                    $query->select('id','title');
                }]);

        $sections = $answers->select(
            'student_id',
            'subject_format_subject_id',
            DB::raw('SUM(score) as total_score'),
        // DB::raw('AVG(score) as score_average') ,
        // DB::raw('count(*) as total_questions'),
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

        return view("school_supervisor.generalQuizReports.skill-level.skills_charts", $data);
    }
}
