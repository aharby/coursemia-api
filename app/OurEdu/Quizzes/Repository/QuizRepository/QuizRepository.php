<?php

namespace App\OurEdu\Quizzes\Repository\QuizRepository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use App\OurEdu\Quizzes\Models\QuizQuestionOption;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizRepository implements QuizRepositoryInterface
{
    use Filterable;

    public $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): QuizRepository
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getAllQuizzes(): LengthAwarePaginator
    {
        return $this->quiz
            ->whereNotNull('published_at')
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getAllQuizzesByUser($user, $filters = []): LengthAwarePaginator
    {
        $model = Quiz::query();
        return $this->applyFilters($model, $filters)
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('created_by', $user->id)
            ->where('creator_role', $user->type)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getAllQuizzesTypesByUser($user, $filters = []): LengthAwarePaginator
    {
        $model = Quiz::query();
        return $this->applyFilters($model, $filters)
            ->where('created_by', $user->id)
            ->where('creator_role', $user->type)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getAllHomeWorksByUser($user, $filters = []): LengthAwarePaginator
    {
        $model = Quiz::query();
        $result = $this->applyFilters($model, $filters)
            ->where('created_by', $user->id)
            ->where('creator_role', $user->type)
            ->where('quiz_type', QuizTypesEnum::HOMEWORK)
            ->orderBy('start_at')
            ->jsonPaginate(env('PAGE_LIMIT', 20));
        return $result;
    }

    public function getAllPeriodicTestsByUser($user, $filters = []): LengthAwarePaginator
    {
        $model = Quiz::query();
        $result = $this->applyFilters($model, $filters)
            ->where('created_by', $user->id)
            ->where('creator_role', $user->type)
            ->where('quiz_type', QuizTypesEnum::PERIODIC_TEST)
            ->orderBy('start_at')
            ->jsonPaginate(env('PAGE_LIMIT', 20));
        return $result;
    }

    public function getSessionQuizzes($classroomSessionId, $isPublished = false): LengthAwarePaginator
    {
        if ($isPublished) {
            return $this->quiz
                ->where('quiz_type', QuizTypesEnum::QUIZ)
                ->whereNotNull('published_at')
                ->where('classroom_class_session_id', $classroomSessionId)
                ->jsonPaginate(env('PAGE_LIMIT', 20));
        }
        return $this->quiz
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('classroom_class_session_id', $classroomSessionId)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getSessionHomework($classroomSessionId, $isPublished = false): LengthAwarePaginator
    {
        if ($isPublished) {
            return $this->quiz
                ->whereNotNull('published_at')
                ->where('classroom_class_session_id', $classroomSessionId)
                ->where('quiz_type', QuizTypesEnum::HOMEWORK)
                ->jsonPaginate(env('PAGE_LIMIT', 20));
        }
        return $this->quiz
            ->where('classroom_class_session_id', $classroomSessionId)
            ->where('quiz_type', QuizTypesEnum::HOMEWORK)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getSessionPeriodicTest($classroomSessionId, $isPublished = false): LengthAwarePaginator
    {
        if ($isPublished) {
            return $this->quiz
                ->whereNotNull('published_at')
                ->where('classroom_class_session_id', $classroomSessionId)
                ->where('quiz_type', QuizTypesEnum::PERIODIC_TEST)
                ->jsonPaginate(env('PAGE_LIMIT', 20));
        }

        return $this->quiz
            ->where('classroom_class_session_id', $classroomSessionId)
            ->where('quiz_type', QuizTypesEnum::PERIODIC_TEST)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }


    public function delete(): bool
    {
        return $this->quiz->delete();
    }

    public function update($data): bool
    {
        return $this->quiz->update($data);
    }

    public function findOrFail($quizId): ?Quiz
    {
        return $this->quiz->findOrFail($quizId);
    }

    public function findOrFailByMultiFields($quizId, $filters): ?Quiz
    {
        return $this->quiz->where($filters)->findOrFail($quizId);
    }

    public function create($data): Quiz
    {
        return $this->quiz->create($data);
    }

    /**
     * @param $questionId
     * @return QuizQuestion
     */
    public function findQuestionOrFail($questionId): ?QuizQuestion
    {
        return QuizQuestion::findOrFail($questionId);
    }

    public function findOptionOrFail($optionId): ?QuizQuestionOption
    {
        return QuizQuestionOption::findOrFail($optionId);
    }

    /**
     * @param $data
     * @return QuizQuestion
     */
    public function createQuestion($data): QuizQuestion
    {
        return QuizQuestion::create($data);
    }

    /**
     * @param $questionId
     * @param $data
     * @return QuizQuestion
     */
    public function updateQuestion($questionId, $data): QuizQuestion
    {
        $question = QuizQuestion::findOrFail($questionId);
        $question->update($data);
        return $question;
    }

    public function createOption($question, $optionData)
    {
        return $question->options()->create($optionData);
    }

    public function updateOption($optionId, $question, $optionData)
    {
        return $question->options()->whereId($optionId)->update($optionData);
    }

    public function getQuestionsIds()
    {
        return $this->quiz->questions()->pluck('id')->toArray();
    }

    public function deleteQuestionsIds(array $questionsIds)
    {
        return $this->quiz->questions()->whereIn('id', $questionsIds)->delete();
    }

    public function deleteDeletedQuestionsOptions(array $questionsIds)
    {
        return QuizQuestionOption::whereIn('quiz_question_id', $questionsIds)->delete();
    }

    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->quiz->questions()->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteOptions($questionId, $optionsIds)
    {
        $question = $this->quiz->questions()->find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }

    public function returnQuestion($page, $questionsOrder): ?LengthAwarePaginator
    {
        $perPage = QuizQuestion::$questionsPerPage;

        $routeName = 'quizzes';
        switch ($this->quiz->quiz_type) {
            case QuizTypesEnum::QUIZ:
                $routeName = 'quizzes';
                $parameterName = 'quizId';
                break;
            case QuizTypesEnum::HOMEWORK:
                $routeName = 'homework';
                $parameterName = 'homeworkId';
                break;
            case QuizTypesEnum::PERIODIC_TEST:
                $routeName = 'periodic-test';
                $parameterName = 'periodicTestId';
                break;
        }

        $questions = $this->quiz->questions()
            ->orderByRaw('FIELD(id,' . $questionsOrder . ')')
            ->with('options')
            ->jsonPaginate($perPage, ['*'], 'page', $page);

        return $questions = $questions->withPath(
            buildScopeRoute(
                "api.student.{$routeName}.get.next-back-questions",
                [
                    $parameterName => $this->quiz->id,
                    'current_question' => $questions->first()->id ?? null
                ]
            )
        );
    }

    public function deleteQuestionAnswers(QuizQuestion $quizQuestion, $studentId)
    {
        $quizQuestion->answers()
            ->where('student_id', $studentId)
            ->delete();
    }

    public function insertAnswer(QuizQuestion $quizQuestion, $answerData)
    {
        $quizQuestion->answers()->create($answerData);
    }

    public function insertManyAnswers(QuizQuestion $quizQuestion, $answers)
    {
        $quizQuestion->answers()->createMany($answers);
    }

    public function listAllQuizStudents($quizId): ?LengthAwarePaginator
    {
        $quiz = Quiz::query()->findOrFail($quizId);

        return $quiz->students()
            ->jsonPaginate();
    }

    public function listQuizStudents($quizId): ?LengthAwarePaginator
    {
        return StudentQuiz::query()
            ->whereHas("student.user", function (Builder $student) {
                $student->whereNull("deleted_at")
                    ->where("is_active", "=", 1);
            })
            ->where('quiz_id', $quizId)
            ->where('status', QuizStatusEnum::FINISHED)
            ->with('quiz')
            ->with('student.user')
            ->jsonPaginate();
    }

    public function getStudentQuiz($quizId, $studentId): ?StudentQuiz
    {
        return StudentQuiz::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->with('quiz')
            ->with('student.user')
            ->first();
    }

    /**
     * @param SchoolAccountBranch $branch
     * @return mixed
     */
    public function listBranchQuizzes(SchoolAccountBranch $branch, Request $request)
    {
        $quizzes = $this->quiz->newQuery()
            ->with("classroom", "subject");
        if ($request->filled('gradeClass') && $request->get('quizType') != QuizTypesEnum::PERIODIC_TEST){
           $quizzes = $quizzes->whereHas("classroom", function (Builder $classroom)  use ($branch, $request){
                $classroom->whereHas("branch", function (Builder $branchQuery) use ($branch) {
                    $branchQuery->where("id", "=", $branch->id);
                });

                if ($request->filled("gradeClass")) {
                    $classroom->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationalSystemGradeClass) use ($request) {
                        $branchEducationalSystemGradeClass->where("grade_class_id", "=", $request->get("gradeClass"));
                    });
                }
            });
        }


        if ($request->filled("classroom")) {
            $quizzes->where("classroom_id", "=", $request->get("classroom"));
        }

        if ($request->filled("session")) {
            $quizzes->where("classroom_class_session_id", "=", $request->get("session"));
        }

        if ($request->filled("instructor")) {
            $quizzes->whereHas("classroomSession", function (Builder $session) use ($request) {
                $session->where("instructor_id", "=", $request->get("instructor"));
            });
        } elseif ($request->filled("date")) {
            $quizzes->where("start_at", ">=", Carbon::parse($request->get("date")))
                ->where("end_at", "<", Carbon::parse($request->get("date"))->addDay());
        }

        if ($request->filled("quizType")) {
            $quizzes->where("quiz_type", "=", $request->get("quizType"));
        }

        $quizzes = $quizzes->whereHas(
                "creator",
                function (Builder $classroom) use ($branch) {
                    $classroom->where('branch_id','=',$branch->id);
                }
            )
            ->with("classroom", "classroomSession.subject")
            ->jsonPaginate(env('PAGE_LIMIT', 20));

        return $quizzes;
    }

    public function getClassroomHomework(Student $student)
    {
        return $this->quiz
            ->where('quiz_type', QuizTypesEnum::HOMEWORK)
            ->whereNotNull('published_at')
            ->where('classroom_id', $student->classroom_id)
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now())
            ->jsonPaginate();
    }

    public function getStudentQuizzesByParent($studentId, $filters = [])
    {
        $model = AllQuizStudent::query()
            ->with(["quiz.subject", "quiz.studentQuiz" => function (HasMany $studentQuiz) use ($studentId) {
                $studentQuiz->where("student_id", "=", $studentId)->first();
            }])
            ->whereHas("quiz", function (Builder $quiz) use ($filters) {
                $quiz->where("end_at", "<", Carbon::now());

                if (array_key_exists("subject_id", $filters) and isset($filters['subject_id'])) {

                    $quiz->where("subject_id", "=", $filters['subject_id']);
                }

            });

        if (array_key_exists("quiz_type", $filters) and isset($filters['quiz_type'])) {
            $model->where("quizzes.quiz_type", "=", $filters['quiz_type']);
        }

            return $model
                ->where('student_id', $studentId)
                ->whereNotNull('quizzes.published_at')
                ->join("quizzes", "quiz_id","=","quizzes.id")
                ->orderByDesc("quizzes.start_at")
                ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getStudentTakenQuizzesByParent($studentId)
    {
        return AllQuizStudent::query()
            ->where('student_id', $studentId)
            ->whereNotNull(['published_at', 'taken_at'])
            ->get();
    }

    // Get student list periodic test is the same
    public function getStudentPeriodicTest(Student $student)
    {
        return $this->quiz
            ->where('quiz_type', QuizTypesEnum::PERIODIC_TEST)
            ->whereNotNull('published_at')
            ->whereHas(
                'allStudentQuiz',
                function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                }
            )
            ->orderBy('start_at','ASC')
            ->where('end_at','>=',now())
            ->jsonPaginate();
    }

    public function getReadyNotifyHomeWorkAndPeriodicTest($interval)
    {
        return $this->quiz
            ->where('is_notified', 0)
            ->where($interval)
            ->whereNotNull('published_at')
            ->whereIn('quiz_type', [QuizTypesEnum::HOMEWORK, QuizTypesEnum::PERIODIC_TEST])
            ->get();
    }

    public function getRunningQuizDetails($homeWorkID, $type = null)
    {
        $quiz = $this->quiz::query()
            ->where("id", "=", $homeWorkID)
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now());

        if ($type) {
            $quiz->where("quiz_type", "=", QuizTypesEnum::HOMEWORK);
        }

        return $quiz->first();
    }

    /**
     * @param SchoolAccount $schoolAccount
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function schoolQuizzes(SchoolAccount $schoolAccount, array $data = [])
    {
        $quizzes = Quiz::query()
            ->with("classroom", "subject", "branch")
            ->whereHas("branch", function (Builder $branch) use ($schoolAccount) {
                $branch->where("school_account_id", "=", $schoolAccount->id);
            });

        if (isset($data['branch'])) {
            $quizzes = $quizzes->where("branch_id", "=", $data["branch"]);
        }

        if (isset($data['quizType'])) {
            $quizzes = $quizzes->where("quiz_type", "=", $data["quizType"]);
        }

        if (isset($data['quizTime'])) {
            $quizzes = $quizzes->where("quiz_time", "=", $data["quizTime"]);
        }

        if (isset($data['created_by'])) {
            $quizzes = $quizzes->where("created_by", "=", $data["created_by"]);
        }

        if (isset($data['subject'])) {
            $quizzes = $quizzes->where("subject_id", "=", $data["subject"]);
        }

        if (isset($data['from'])) {
            $quizzes = $quizzes->where("start_at", ">=", $data["from"]);
        }

        if (isset($data['to'])) {
            $quizzes = $quizzes->where("end_at", "<=", $data["to"]);
        }

        $quizzes = $quizzes->paginate(env("PAGE_LIMIT", 20));

        return $quizzes;
    }
}
