<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolManager\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\StudentQuizzesExport;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\StudentSectionPerformanceExport;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports\StudentLevelReportExport;

class StudentLevelReportsController extends BaseController
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * StudentLevelReportsController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
    }


    public function students(Request $request)
    {
        list($schoolStudents, $classrooms,, $gradeClasses) = $this->studentsData($request);

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
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        }

        $classrooms = [];
        $instructors = [];
        $subjects = [];
        $sessions = [];

        $schoolStudents = Student::query()->with("classroom", "gradeClass", "user")
            ->whereHas('user', function (Builder $users) use($request){
                $users->where("is_active", "=", true);
            });

            $classroomsIDs = Classroom::query();

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
            $schoolStudents = $schoolStudents->whereHas('gradeClass',function(Builder $query) use($request){
                $query->where('class_id',$request->get('gradeClass'));
            });
        }



        if ($request->filled("branch_id")) {
            $classroomsIDs = $classroomsIDs->where("branch_id", $request->get('branch_id'))
                ->pluck("id")->toArray();
        }else{
            $classroomsIDs = $classroomsIDs->whereIn("branch_id", $branchesIDs)
                ->pluck("id")->toArray();
        }


        if ($request->filled("classroom")) {
            $schoolStudents = $schoolStudents->whereHas('classroom',function(Builder $query) use($request){
                $query->where('classroom_id',$request->get('classroom'));
            });
        }else{
            $schoolStudents = $schoolStudents->whereIn("classroom_id", $classroomsIDs);
        }

        $schoolStudents = $schoolStudents->paginate()->withQueryString();
        $data['filters'] = str_replace('page=','',explode("?",$request->getRequestUri())[1]??'');
        $data["branches"] = $request->user()->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.student level report");
        $data['students'] = $schoolStudents;
        $data['gradeClasses'] = $gradeClasses;

        return view("school_account_manager.generalQuizReports.student_level.students", $data);
    }

    public function studentsExport(Request $request)
    {
        list($schoolStudents) = $this->studentsData($request);

        $data['rows'] = $schoolStudents->get();

        return Excel::download(new StudentLevelReportExport($data['rows']), "student level report.xls");
    }

    public function studentQuizzes(User $student,Request $request)
    {
        $requestData = $request->all();
        if(!$request->has('branch_id')){
            $user = Auth::user();
            $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

            $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

            if ($branch and !in_array($branch->id, $branchesIDs)) {
                return unauthorize();
            }

            if (!$branch) {
                $requestData['branch_id'] = $branchesIDs;
            }
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzes($requestData,true)
                ->whereHas('studentsAnswered',function($query) use($student){
                    $query->where('student_id','=',$student->id);
                })->pluck('id')->toArray();

        $studentQuizzes = GeneralQuizStudent::query()
            ->with("generalQuiz", "subject")
            ->whereIn('general_quiz_id',$generalQuizzes)
            ->where('student_id','=',$student->id)
            ->paginate()->withQueryString();

        $data['studentQuizzes'] = $studentQuizzes;
        $data["page_title"] = $student->name;
        $data["user"] = $student;

        return view("school_account_manager.generalQuizReports.student_level.student_quizzess", $data);
    }


    public function studentQuizzesExport(User $student,Request $request)
    {
        $requestData = $request->all();
        if(!$request->has('branch_id')){
            $user = Auth::user();
            $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

            $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

            if ($branch and !in_array($branch->id, $branchesIDs)) {
                return unauthorize();
            }

            if (!$branch) {
                $requestData['branch_id'] = $branchesIDs;
            }
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzes($requestData,true)
                ->whereHas('studentsAnswered',function($query) use($student){
                    $query->where('student_id','=',$student->id);
                })->pluck('id')->toArray();

        $studentQuizzes = GeneralQuizStudent::query()
            ->with("generalQuiz", "subject")
            ->whereIn('general_quiz_id',$generalQuizzes)
            ->where('student_id','=',$student->id)
            ->get();

        return Excel::download(new StudentQuizzesExport($studentQuizzes), $student->name . "_report.xls");
    }



    public function studentSectionPerformance(GeneralQuizStudent $generalQuizStudent)
    {
        $studentSectionsGrade =
            GeneralQuizStudentAnswer::query()
                ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
                ->with([
                    'section'=>function($query){
                        $query->select('id','title');
                    }])->where("student_id", "=", $generalQuizStudent->student_id)
                ->select(
                    'student_id',
                    'subject_format_subject_id',
                    DB::raw('SUM(score) as total_score'),
                )
                ->groupBy('subject_format_subject_id','student_id')
                ->get()->groupBy('section.title');


        $sectionsStudentsGrade = GeneralQuizStudentAnswer::query()
            ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
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

        $sectionGrades = GeneralQuizQuestionBank::with('section')
            ->whereHas('generalQuiz',function($query)use($generalQuizStudent){
                $query->where('general_quiz_id',$generalQuizStudent->general_quiz_id);
            })->get()->groupBy('section.title');


        $data["studentSectionsGrade"] = $studentSectionsGrade;
        $data["sectionsStudentsGrade"] = $sectionsStudentsGrade;
        $data["sectionGrades"] = $sectionGrades;
        $data["generalQuizStudent"] = $generalQuizStudent;
        $data["page_title"] = $generalQuizStudent->student->name ?? '';

        return view("school_account_manager.generalQuizReports.student_level.student_sections", $data);
    }

    /**
     * Get students data
     * @param Illuminate\Http\Request $request
     * @return array
     */
    private function studentsData(Request $request)
    {
        $user = Auth::user();
        $branchesIDs = $user->schoolAccount->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }
        $gradeClasses = [];
        if ($branch) {
            $gradeClasses = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        }

        $classrooms = [];

        $schoolStudents = Student::query()->with("classroom", "gradeClass", "user")
        ->whereHas('user', function (Builder $users) use ($request) {
            $users->where("is_active", "=", true);
        });

        $classroomsIDs = Classroom::query();

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })
                ->pluck("name", "id");
            $schoolStudents = $schoolStudents->whereHas('gradeClass', function (Builder $query) use ($request) {
                $query->where('class_id', $request->get('gradeClass'));
            });
        }



        if ($request->filled("branch_id")) {
            $classroomsIDs = $classroomsIDs->where("branch_id", $request->get('branch_id'))
            ->pluck("id")->toArray();
        } else {
            $classroomsIDs = $classroomsIDs->whereIn("branch_id", $branchesIDs)
                ->pluck("id")->toArray();
        }


        if ($request->filled("classroom")) {
            $schoolStudents = $schoolStudents->whereHas('classroom', function (Builder $query) use ($request) {
                $query->where('classroom_id', $request->get('classroom'));
            });
        } else {
            $schoolStudents = $schoolStudents->whereIn("classroom_id", $classroomsIDs);
        }

        return [$schoolStudents, $classrooms, $branch, $gradeClasses];
    }

    public function studentSectionPerformanceExport(GeneralQuizStudent $generalQuizStudent)
    {
        $studentSectionsGrade =
            GeneralQuizStudentAnswer::query()
                ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
                ->with([
                    'section'=>function($query){
                        $query->select('id','title');
                    }])->where("student_id", "=", $generalQuizStudent->student_id)
                ->select(
                    'student_id',
                    'subject_format_subject_id',
                    DB::raw('SUM(score) as total_score'),
                )
                ->groupBy('subject_format_subject_id','student_id')
                ->get()->groupBy('section.title');


        $sectionsStudentsGrade = GeneralQuizStudentAnswer::query()
            ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
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

        $sectionGrades = GeneralQuizQuestionBank::with('section')
            ->whereHas('generalQuiz',function($query)use($generalQuizStudent){
                $query->where('general_quiz_id',$generalQuizStudent->general_quiz_id);
            })->get()->groupBy('section.title');

        return Excel::download(new StudentSectionPerformanceExport($studentSectionsGrade, $generalQuizStudent, $sectionsStudentsGrade, $sectionGrades), "student_section_performance_export.xls");
    }
}
