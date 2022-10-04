<?php


namespace App\OurEdu\Quizzes\Controllers;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends BaseController
{
    /**
     * @var QuizRepositoryInterface
     */
    private $quizRepository;

    /**
     * QuizController constructor.
     * @param QuizRepositoryInterface $quizRepository
     */
    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function listAllQuizzes(Request $request)
    {
        $user = Auth::user();
        $branch = $user->branch;
        $data = [];

        $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();

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
            $instructorsData = User::query()
                ->whereHas("schoolInstructorSessions" , function (Builder $sessions) use ($request) {
                    $sessions->where("classroom_id", "=", $request->get("classroom"));
                })
                ->get();

            foreach ($instructorsData as $instructor) {
                $instructors[$instructor->id] = $instructor->first_name . " " . $instructor->last_name;
            }
        }

        if ($request->filled("classroom") and $request->filled("instructor")) {
            $classSessions = ClassroomClassSession::query()
                ->with("subject")
                ->where("instructor_id", "=", $request->get("instructor"))
                ->where("classroom_id", "=", $request->get("classroom"));
            if ($request->filled("date")) {
                $classSessions->where("from", ">=", Carbon::parse($request->get("date")))
                    ->where("to", "<", Carbon::parse($request->get("date"))->addDay());
            }

            $classSessions = $classSessions->get();
            $sessions = [];
            foreach ($classSessions as $session) {
                $sessions[$session->id] = $session->subject->name . ' - ' . $session->from_date . ' - (' . $session->from_time . ' - ' . $session->to_time . ')';
            }
        }

        $data["sessions"] = $sessions;
        $data["instructors"] = $instructors;
        $data["classrooms"] = $classrooms;
        $data["quizzes"] = $this->quizRepository->listBranchQuizzes($branch, $request);
        $data["quizTimes"] = QuizTimesEnum::class;
        $data["page_title"] = trans("navigation.quizzes");

        return view("school_supervisor.quizzes.index", $data);
    }

    public function quizStudents(Quiz $quiz)
    {
        $data["students"] = $this->quizRepository->listQuizStudents($quiz->id);
        $data["page_title"] = trans("quiz.quiz Grades");

        return view("school_supervisor.quizzes.students", $data);
    }
}
