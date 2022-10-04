<?php


namespace App\OurEdu\Quizzes\SchoolManager\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use Carbon\Carbon;
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
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    private $title;

    /**
     * QuizController constructor.
     * @param QuizRepositoryInterface $quizRepository
     */
    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
        $this->title = trans("quiz.reports");
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            "from" => "nullable|date|before:now",
            "to" => "nullable|date|after_or_equal:from|before:now"
        ], [], [
            'from' => trans("reports.from"),
            'to' => trans("reports.to"),
        ]);

        $user = Auth::user();
        $requestData = $request->all();

        if ($request->filled("from")) {
            $requestData["from"] = Carbon::parse($request->get("from"));
        }

        if (!$request->get("to") || ($request->filled("to") and Carbon::yesterday()->lt(Carbon::parse($request->get("to")))))
        {
            $requestData['to'] = now();
        } else {
            $requestData['to'] = Carbon::parse($request->get("to"))->format("Y-m-d 23:59");
        }

        if ($request->filled("quizType") and array_search($request->get("quizType"), [QuizTimesEnum::AFTER_SESSION, QuizTimesEnum::PRE_SESSION]) !== false)
        {
            $requestData["quizTime"] = $request->get("quizType");
            $requestData["quizType"] = QuizTypesEnum::QUIZ;
        }

        $data = [];

        $data["quizTypes"] = [
            QuizTypesEnum::PERIODIC_TEST => QuizTypesEnum::getLabel(QuizTypesEnum::PERIODIC_TEST),
            QuizTypesEnum::HOMEWORK => QuizTypesEnum::getLabel(QuizTypesEnum::HOMEWORK),
            QuizTimesEnum::PRE_SESSION => QuizTimesEnum::getLabel(QuizTimesEnum::PRE_SESSION),
            QuizTimesEnum::AFTER_SESSION => QuizTimesEnum::getLabel(QuizTimesEnum::AFTER_SESSION)
        ];

        if ($request->filled("branch")) {
            $branch = SchoolAccountBranch::query()->findOrFail($request->get("branch"));

            $instructorObj= User::query()
                ->whereHas("quizzes", function (Builder $quiz) use ($branch) {
                    $quiz->where("branch_id", "=", $branch->id);
                })
                ->get();

            $instructors = [];
            foreach ($instructorObj as $instructor) {
                $instructors[$instructor->id] = $instructor->name . " (" . $instructor->type . ")";
            }
            $data["instructors"] = $instructors;
            $branchEducationalSystemsIDs = $branch->branchEducationalSystem()->pluck("educational_system_id")->toArray();
            $branchEducationalSystemsAcademicYears = $branch->branchEducationalSystem()->pluck("academic_year_id")->toArray();
            $branchEducationalSystemsEdcayionalTerms = $branch->branchEducationalSystem()->pluck("educational_term_id")->toArray();

            $educationalSystem = EducationalSystem::query()->whereIn("id", $branchEducationalSystemsIDs)->pluck("id")->toArray();

            $data["subjects"] = Subject::query()
                ->whereIn('educational_system_id', $educationalSystem)
                ->whereIn('academical_years_id', $branchEducationalSystemsAcademicYears)
                ->whereIn('educational_term_id', $branchEducationalSystemsEdcayionalTerms)
                ->pluck("name", "id");
        }


        $data['quizzes'] = $this->quizRepository->schoolQuizzes($user->schoolAccount, $requestData);
        $data["branches"] = $user->schoolAccount->branches()->pluck('name' , 'id')->toArray();
        $data["page_title"] = $this->title;

        return view("school_account_manager.quizzes.index", $data);
    }

    public function students(Quiz $quiz)
    {
        $data["students"] = $this->quizRepository->listQuizStudents($quiz->id);
        $data["page_title"] = trans("quiz.quiz Grades");

        return view("school_account_manager.quizzes.students", $data);
    }
}
