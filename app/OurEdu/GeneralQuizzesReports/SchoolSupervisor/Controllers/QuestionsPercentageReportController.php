<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzesReports\Exports\ExportQuestionsPercentageReportDetails;
use App\OurEdu\GeneralQuizzesReports\Exports\ExportQuestionsPercentageReport;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class QuestionsPercentageReportController extends BaseController
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
     * QuestionsPercentageReportController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param SubjectRepositoryInterface $subjectRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function index(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();
        $branch = $user->branch;
        $requestData['branch_id'] = $branch->id;
        $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();

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
        $data["page_title"] = trans("navigation.questions Percentages");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);

        return view("school_supervisor.generalQuizReports.branch_reports.question_percentage.index", $data);
    }


    public function questions(GeneralQuiz $generalQuiz)
    {
        $questions = $generalQuiz->questions()
            ->with(["groupStudentAnswersByQuestion" => function (HasMany $studentAnswers) use ($generalQuiz) {
                $studentAnswers
                    ->where("general_quiz_id", "=", $generalQuiz->id)
                    ->selectRaw("AVG(score) as score_average, SUM(score) as total_score, general_quiz_question_id");
            },
                "groupStudentAnswersByStudent" => function (HasMany $studentAnswers) use ($generalQuiz) {
                    $studentAnswers
                        ->where("general_quiz_id", "=", $generalQuiz->id)
                        ->groupBy("general_quiz_question_id")
                        ->selectRaw("student_id, general_quiz_question_id");
                },
                "section",
            ])
            ->paginate(env("PAGE_LIMIT"));

        $data['questions'] = $questions;

        $data['generalQuiz'] = $generalQuiz;

        return view("school_account_manager.generalQuizReports.branch_reports.question_percentage.questions", $data);
    }

    public function export(Request $request)
    {
        $requestData = $request->all();

        $user = Auth::user();

        $branch = $user->branch;
        $requestData['branch_id'] = $branch->id;

        $data = $this->generalQuizRepository->exportGeneralQuizzes($requestData);

        return Excel::download(new ExportQuestionsPercentageReport($data), 'questions_percentage_report.xls');
    }

    public function exportQuestions(GeneralQuiz $generalQuiz)
    {
        $questions = $generalQuiz->questions()
            ->with(["groupStudentAnswersByQuestion" => function (HasMany $studentAnswers) use ($generalQuiz) {
                $studentAnswers
                    ->where("general_quiz_id", "=", $generalQuiz->id)
                    ->selectRaw("AVG(score) as score_average, SUM(score) as total_score, general_quiz_question_id");
            },
                "groupStudentAnswersByStudent" => function (HasMany $studentAnswers) use ($generalQuiz) {
                    $studentAnswers
                        ->where("general_quiz_id", "=", $generalQuiz->id)
                        ->groupBy("general_quiz_question_id")
                        ->selectRaw("student_id, general_quiz_question_id");
                },
                "section",
            ])
            ->get();

            return Excel::download(new ExportQuestionsPercentageReportDetails($questions,$generalQuiz), 'questions_percentage_details_report.xls');
    }
}
