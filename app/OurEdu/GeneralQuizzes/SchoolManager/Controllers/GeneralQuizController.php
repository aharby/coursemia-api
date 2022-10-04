<?php


namespace App\OurEdu\GeneralQuizzes\SchoolManager\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\SchoolManager\Exports\GeneralQuizStudentScoreExport;
use App\OurEdu\GeneralQuizzes\SchoolManager\Exports\ListGeneralQuizzesExport;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\GeneralUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use function Symfony\Component\String\s;

class GeneralQuizController extends BaseController
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var UpdateHomeworkUseCaseInterface
     */
    private $updateHomeworkUseCase;


    /**
     * @var GeneralUseCaseInterface
     */

    private $GeneralQuizUserCase;

    /**
     * GeneralQuizController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param UpdateHomeworkUseCaseInterface $updateHomeworkUseCase
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository,
                                UpdateHomeworkUseCaseInterface $updateHomeworkUseCase,
                                GeneralUseCaseInterface        $GeneralQuizUserCase)
    {
        $this->generalQuizRepository = $generalQuizRepository;

        $this->updateHomeworkUseCase = $updateHomeworkUseCase;

        $this->GeneralQuizUserCase = $GeneralQuizUserCase;
    }

    public function index(Request $request)
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
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        }

        $classrooms = [];
        $instructors = [];
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

                $instructors = DB::table('users')
                    ->select(['users.id', 'classroom_class_sessions.instructor_id', DB::raw('concat_ws( " ",first_name , last_name ) AS name')])
                    ->distinct('classroom_class_sessions.classroom_id')
                    ->join('classroom_class_sessions', 'users.id', '=', 'classroom_class_sessions.instructor_id')
                    ->where("classroom_class_sessions.classroom_id", "=", $request->get("classroom"))
                    ->whereNull('users.deleted_at')
                    ->whereNull('classroom_class_sessions.deleted_at')
                    ->get()->pluck('name', 'instructor_id')->toArray();

        }

        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.quizzes");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_account_manager.generalQuizzes.index", $data);
    }

    public function getTrashedClassrooms(): View
    {
        $schoolAccount = auth()->user()->schoolAccount;

        $classrooms = $schoolAccount->classrooms()
            ->onlyTrashed()
            ->with('branch')
            ->paginate();

        $data['rows'] = $classrooms;
        return view('school_account_manager.generalQuizzes.trashed-classrooms', $data);
    }

    public function indexTrashed($classroomId): View
    {
        $data['generalQuizzes'] =$this->generalQuizRepository->trashedClassroomGeneralQuizzes($classroomId);

        return view("school_account_manager.generalQuizzes.trashed", $data);
    }

    public function exportTrashed($classroomId)
    {
        $generalQuizzes =$this->generalQuizRepository->trashedClassroomGeneralQuizzes($classroomId,false);

        return Excel::download(new ListGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }

    public function exportList(Request $request, SchoolAccountBranch $branch = null)
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

        return Excel::download(new ListGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }

    public function  students(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score','student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_account_manager.generalQuizzes.students", $data);
    }

    public function  trashedClassroomStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->get()->keyBy('student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->trashedStudents($generalQuiz,true);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_account_manager.generalQuizzes.trashed-students", $data);
    }

    public function  exportStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->pluck('score','student_id')->toArray();

        if($generalQuiz->students()->count()>0){
            $students = $generalQuiz->students()->get();
        } else {
            $students = $this->generalQuizRepository->students($generalQuiz);
        }

        return Excel::download(
            new GeneralQuizStudentScoreExport($students, $generalQuiz),
            $generalQuiz->title . "-students.xls"
        );
    }
    public function  exportStudentsGrades(GeneralQuiz $generalQuiz)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($generalQuiz);

        return Excel::download(new GeneralQuizQuestionsScoresExport($grades, $generalQuiz ) ,
            $generalQuiz->title . "-students_scores.xls" );

    }

    public function delete(GeneralQuiz $generalQuiz)
    {
        $usecase = $this->GeneralQuizUserCase->delete($generalQuiz);
        if($usecase['code'] != 200){
            return redirect()->back()->with(["error" => $usecase['message']]);
        }
        return redirect()->back()->with(["success" =>  $usecase['message']]);
    }
}
