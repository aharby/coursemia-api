<?php


namespace App\OurEdu\SchoolAdmin\GeneralQuizzes\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Exports\GeneralQuizStudentScoreExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Exports\ListGeneralQuizzesExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories\GeneralQuizRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\UseCases\GeneralQuizUseCase;

class GeneralQuizController extends BaseController
{
    private $generalQuizRepository;
     
    public function __construct()
    {
        $this->generalQuizRepository = new GeneralQuizRepository();
        }

    public function index(Request $request)
    {
        $data = [];
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
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck(
                'gradeClasses'
            )->flatten();
        }

        $classrooms = [];
        $instructors = [];
        $sessions = [];

        if ($request->filled("gradeClass")) {
            $classrooms = $branch
                ->classrooms()
                ->whereHas(
                    "branchEducationalSystemGradeClass",
                    function (Builder $branchEducationSystemGrade) use ($request) {
                        $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                    }
                )
                ->pluck("name", "id");
        }

        if ($request->filled("classroom")) {
            $instructors = DB::table('users')
                ->select(
                    [
                        'users.id',
                        'classroom_class_sessions.instructor_id',
                        DB::raw('concat_ws( " ",first_name , last_name ) AS name')
                    ]
                )
                ->distinct('classroom_class_sessions.classroom_id')
                ->join('classroom_class_sessions', 'users.id', '=', 'classroom_class_sessions.instructor_id')
                ->where("classroom_class_sessions.classroom_id", "=", $request->get("classroom"))
                ->whereNull('users.deleted_at')
                ->whereNull('classroom_class_sessions.deleted_at')
                ->get()->pluck('name', 'instructor_id')->toArray();
        }

        $data["branches"] = $user->schoolAdmin->currentSchool->branches()->pluck('name', 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.quizzes");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_admin.generalQuizzes.index", $data);
    }

    public function exportList(Request $request, SchoolAccountBranch $branch = null)
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

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new ListGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }

    public function students(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score', 'student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_admin.generalQuizzes.students", $data);
    }

    public function exportStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score', 'student_id')->toArray();

        if ($generalQuiz->students()->count() > 0) {
            $students = $generalQuiz->students()->get();
        } else {
            $students = $this->generalQuizRepository->students($generalQuiz);
        }

        return Excel::download(
            new GeneralQuizStudentScoreExport($students, $generalQuiz),
            $generalQuiz->title . "-students.xls"
        );
    }

    public function exportStudentsGrades(GeneralQuiz $generalQuiz)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($generalQuiz);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $generalQuiz),
            $generalQuiz->title . "-students_scores.xls"
        );
    }

    public function delete(GeneralQuiz $generalQuiz)
    {
        $usecase = (new GeneralQuizUseCase())->delete($generalQuiz);
        if($usecase['code'] != 200){
            return redirect()->back()->with(["error" => $usecase['message']]);
        }
        return redirect()->back()->with(["success" =>  $usecase['message']]);
    }
}
