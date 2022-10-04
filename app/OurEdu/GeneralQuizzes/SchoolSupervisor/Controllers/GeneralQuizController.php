<?php


namespace App\OurEdu\GeneralQuizzes\SchoolSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\SchoolSupervisor\Exports\GeneralQuizStudentScoreExport;
use App\OurEdu\GeneralQuizzes\SchoolSupervisor\Exports\ListGeneralQuizzesExport;
use App\OurEdu\GeneralQuizzes\SchoolSupervisor\Exports\TrashedGeneralQuizzesExport;
use App\OurEdu\GeneralQuizzes\SchoolSupervisor\Exports\TrashedStudentScoreExport;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\GeneralUseCaseInterface;
use App\OurEdu\SchoolAccounts\Classroom;
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

    public function index(Request $request, SchoolAccountBranch $branch = null)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $branch ?? $user->branch;
        $requestData['branch_id'] = $branch->id;
        $data = [];

        $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();

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


        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.quizzes");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_supervisor.generalQuizzes.index", $data);
    }

    public function indexTrashed($classroomId): View
    {
        $data['generalQuizzes'] =$this->generalQuizRepository->trashedClassroomGeneralQuizzes($classroomId);

        return view("school_supervisor.generalQuizzes.trashed", $data);
    }

    public function exportTrashed($classroomId)
    {
        $generalQuizzes =$this->generalQuizRepository->trashedClassroomGeneralQuizzes($classroomId,false);

        return Excel::download(new TrashedGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }
    
    public function exportList(Request $request, SchoolAccountBranch $branch = null)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $branch ?? $user->branch;
        $requestData['branch_id'] = $branch->id;

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new ListGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }

    public function  students(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->get()->keyBy('student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_supervisor.generalQuizzes.students", $data);
    }

    public function  trashedClassroomStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->get()->keyBy('student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->trashedStudents($generalQuiz,true);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_supervisor.generalQuizzes.trashed-students", $data);
    }
    
    public function  exportStudents(GeneralQuiz $generalQuiz)
    {
        if (request()->trashed){
            $students = $this->generalQuizRepository->trashedStudents($generalQuiz);
            return Excel::download(
                new TrashedStudentScoreExport($students, $generalQuiz),
                str_replace(['/',"\\"],'',$generalQuiz->title) . "-students.xls"
            );
        }
        
        if($generalQuiz->students()->count()>0){
            $students = $generalQuiz->students()->get();
        }
        else {
            $students = $this->generalQuizRepository->students($generalQuiz);
        }

        return Excel::download(
            new GeneralQuizStudentScoreExport($students, $generalQuiz),
            str_replace(['/',"\\"],'',$generalQuiz->title) . "-students.xls"
        );
    }

    public function publish(GeneralQuiz $generalQuiz)
    {
        $useCase = $this->updateHomeworkUseCase->publishHomework($generalQuiz);

        if ($useCase['status'] == 200) {
            return redirect()->back()->with(['success' => trans('general_quizzes.published Successfully')]);
        } else {
            return redirect()->back()->with(['error' => $useCase['detail']]);
        }
    }

    public function deactivate(GeneralQuiz $generalQuiz)
    {
        $useCase = $this->updateHomeworkUseCase->deactivateHomework($generalQuiz);

        if ($useCase['status'] == 200) {
            return redirect()->back()->with(['success' => trans('general_quizzes.Deactivated successfully')]);
        } else {
            return redirect()->back()->with(['error' => $useCase['detail']]);
        }
    }

    public function toggleShowingResultFlag(GeneralQuizStudent $student)
    {
        if ($student->generalQuiz->quiz_type != GeneralQuizTypeEnum::PERIODIC_TEST) {
            return redirect()->back()->with(["success" => trans("general_quizzes.this is not a periodic Test")]);
        }

        $student->show_result = !$student->show_result;
        $student->save();

        $trueCount = $student->generalQuiz->studentsAnswered()->where('show_result',true)->count();

        $student->generalQuiz->show_result = $trueCount > 0;
        $student->generalQuiz->save();

        return redirect()->back()->with(["success" => trans("quiz.the show result status has changed")]);
    }

    public function  exportStudentsGrades(GeneralQuiz $generalQuiz)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($generalQuiz);

        return Excel::download(new GeneralQuizQuestionsScoresExport($grades, $generalQuiz ) ,
            $generalQuiz->title . "-students_scores.xls" );

    }

    public function toggleShowingResultFlagOnAll(GeneralQuiz $generalQuiz)
    {
        if ($generalQuiz->quiz_type != GeneralQuizTypeEnum::PERIODIC_TEST) {
            return redirect()->back()->with(["success" => trans("general_quizzes.this is not a periodic Test")]);
        }

        $generalQuiz->show_result = !$generalQuiz->show_result;
        $generalQuiz->save();

        $generalQuiz->studentsAnswered()->update(['show_result'=> $generalQuiz->show_result]);

        return redirect()->back()->with(["success" => trans("quiz.the show result status has changed")]);
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
