<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories;

use Carbon\Carbon;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;

class GeneralQuizRepository
{
    use Filterable;

    public $generalQuiz;

    public function __construct()
    {
        $this->generalQuiz = new GeneralQuiz();
    }


    public function create($data): GeneralQuiz
    {
        return $this->generalQuiz->create($data);
    }

    public function update($data): bool
    {
        return $this->generalQuiz->update($data);
    }

    public function delete(): bool
    {
        return $this->generalQuiz->delete();
    }

    public function findOrFail($generalQuizId): ?GeneralQuiz
    {
        return $this->generalQuiz->findOrFail($generalQuizId);
    }

    public function findOrFailByMultiFields($generalQuizId, $filters): ?GeneralQuiz
    {
        return $this->generalQuiz->where($filters)->findOrFail($generalQuizId);
    }

    public function getGeneralQuiz()
    {
        return $this->generalQuiz;
    }

    public function setGeneralQuiz(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
        return $this;
    }

    public function saveGeneralQuizClassrooms(GeneralQuiz $generalQuiz, $classroomIds)
    {
        $generalQuiz->classrooms()->sync($classroomIds);
        return $generalQuiz;
    }

    public function getGeneralQuizStudents(GeneralQuiz $generalQuiz)
    {
        if ($generalQuiz->students()->count() > 0) {
            return $generalQuiz->students()->paginate();
        }
        return $this->students($generalQuiz, true);
    }

    public function saveGeneralQuizSections(GeneralQuiz $generalQuiz, $sectionsIds)
    {
        $generalQuiz->sectionsRelations()->sync($sectionsIds);
        return $generalQuiz;
    }

    // studentids - user_id
    public function saveGeneralQuizStudents(GeneralQuiz $generalQuiz, $studentIds)
    {
        $generalQuiz->students()->sync($studentIds);
        return $generalQuiz;
    }

    public function listStudentAvailableGeneralQuizzes($quizType, array $filters = [])
    {
        $model = $this->applyFilters(new GeneralQuiz(), $filters);
        return $model
            ->whereNotNull('published_at')
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now())
            ->where('quiz_type', $quizType)
            ->active()
            ->where(
                function ($query) {
                    $query->whereHas(
                        'students',
                        function ($q) {
                            $q->where('id', auth()->user()->id);
                        }
                    )
                        ->orWhereHas(
                            'classrooms',
                            function ($qu) {
                                $qu->where('id', auth()->user()->student->classroom_id);
                            }
                        )
                        ->whereDoesntHave("students");
                }
            )
            ->orderByDesc("start_at")
            ->paginate();
    }

    public function getGeneralQuizQuestions(GeneralQuiz $quiz)
    {
        return $quiz->questions()->with("questions", "sections")->get();
    }

    public function getGeneralQuizQuestionsPaginated(GeneralQuiz $quiz)
    {
        return $quiz->questions()->with("questions", "sections")->paginate(5);
    }


    public function returnQuestion(int $page, $questionsOrder): ?LengthAwarePaginator
    {
        $perPage = GeneralQuizQuestionBank::$questionsPerPage;
        $routeName = 'general-quizzes';

        $questions = $this->generalQuiz->questions();
        if (!empty($questionsOrder)) {
            $questions = $questions->orderByRaw('FIELD(id,' . $questionsOrder . ')');
        }

        $questions = $questions
            ->with(['questions'])
            ->jsonPaginate($perPage, ['*', 'general_quiz_question_bank.question_id'], 'page', $page);

        return $questions = $questions->withPath(
            buildScopeRoute(
                "api.{$routeName}.homework.student.get.next-back-questions",
                [
                    'homeworkId' => $this->generalQuiz->id,
                    'current_question' => $questions->first()->id ?? null
                ]
            )
        );
    }

    public function returnQuestionsViewAs(int $page): ?LengthAwarePaginator
    {
        $perPage = GeneralQuizQuestionBank::$questionsPerPage;
        $routeName = 'general-quizzes';

        $questions = $this->generalQuiz->questions();

        return $questions
            ->with(['questions'])
            ->jsonPaginate($perPage, ['*', 'general_quiz_question_bank.question_id'], 'page', $page);
    }

    /**
     * @param GeneralQuiz $generalQuiz
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function students(GeneralQuiz $generalQuiz, $paginate = null)
    {
        $classroomsID = $generalQuiz->classrooms()->pluck("id")->toArray();
        $students = $generalQuiz->students();
        if (!$students->count()) {
            $students = User::query()
                ->whereHas(
                    "student",
                    function (Builder $builder) use ($classroomsID) {
                        $builder->whereIn("classroom_id", $classroomsID);
                    }
                );
        }
        if (!is_null($paginate)) {
            return $students->paginate();
        }
        return $students->get();
    }

    public function updateGeneralQuizMark(GeneralQuiz $generalQuiz)
    {
        $quizMark = $generalQuiz->questions()->pluck('grade')->sum();
        return $generalQuiz->update(['mark' => $quizMark]);
    }

    public function listInstructorGeneralQuizzes(
        User $instructor,
        $quizType,
        $subject_id = null,
        $gradeClassId = null,
        $date = null,
        $report = false
    ) {
        // GeneralQuizTypeEnum::HOMEWORK
        $homeworks = GeneralQuiz::query()
            ->where("quiz_type", "=", $quizType)
            ->where("created_by", "=", $instructor->id);
        if ($subject_id) {
            $homeworks->where("subject_id", "=", $subject_id);
        }
        if ($gradeClassId) {
            $homeworks->where("grade_class_id", "=", $gradeClassId);
        }
        if ($date) {
            $date = (new Carbon($date))->format('Y-m-d H:i:s');
            $homeworks->where('start_at', '<=', $date)
                ->where('end_at', '>', $date);
        }
        if ($report) {
            $homeworks = $homeworks->whereNotNull('published_at');
        }
        $homeworks = $homeworks->orderBy('start_at', 'DESC')
            ->paginate(env("PAGE_LIMIT"));

        return $homeworks;
    }

    public function listInstructorGeneralQuizzesWithoutPagination(
        User $instructor,
        $quizType,
        $subject_id = null,
        $gradeClassId = null,
        $date = null,
        $report = false
    ) {
        // GeneralQuizTypeEnum::HOMEWORK
        $homeworks = GeneralQuiz::query()
            ->where("quiz_type", "=", $quizType)
            ->where("created_by", "=", $instructor->id);
        if ($subject_id) {
            $homeworks->where("subject_id", "=", $subject_id);
        }
        if ($gradeClassId) {
            $homeworks->where("grade_class_id", "=", $gradeClassId);
        }
        if ($date) {
            $date = (new Carbon($date))->format('Y-m-d H:i:s');
            $homeworks->where('start_at', '<=', $date)
                ->where('end_at', '>', $date);
        }
        if ($report) {
            $homeworks = $homeworks->whereNotNull('published_at');
        }
        $homeworks = $homeworks->orderBy('start_at', 'DESC')->get();

        return $homeworks;
    }

    public function hasEssayQuestion()
    {
        $count = $this->generalQuiz->questions()->where('slug', '=', QuestionsTypesEnums::ESSAY)
            ->pluck('id')->count();
        return $count > 0 ? true : false;
    }

    public function listEducationalSupervisorGeneralQuizzes(
        $eduSupervisor,
        $filters,
        $classroom = null,
        $type = null,
        $instructor = null,
        $date = null
    ) {
        $branches = $eduSupervisor->branches->pluck('id')->toArray() ?? [];

        $subjects = $eduSupervisor->educationalSupervisorSubjects->pluck('id')->toArray();
        $gradeClasses = $eduSupervisor->educationalSupervisorSubjects->pluck('gradeClass.id')->toArray();

        $generalQuiz = $this->applyFilters(new GeneralQuiz(), $filters)
            ->whereIn('branch_id', $branches)
            ->whereIn('subject_id', $subjects)
            ->whereIn('grade_class_id', $gradeClasses);

        if (!is_null($date)) {
            $date = (new \Carbon\Carbon($date))->format('Y-m-d H:i:s');
            $generalQuiz = $generalQuiz->where('start_at', '>=', $date);
        }
        if (!is_null($classroom)) {
            $generalQuiz = $generalQuiz->whereHas(
                'classrooms',
                function ($q) use ($classroom) {
                    $q->where('classrooms.id', $classroom);
                }
            );
        }
        if (!is_null($instructor)) {
            $generalQuiz = $generalQuiz->where('created_by', $instructor);
        }

        if (!is_null($type)) {
            $generalQuiz = $generalQuiz->where('quiz_type', "=", $type);
        }


        return $generalQuiz->orderByDesc("start_at")->jsonPaginate();
    }

    public function ListFormativeTest()
    {
        $formativeTest = GeneralQuiz::query()
            ->where('quiz_type',"=", GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->where('created_by', "=", auth()->id())
            ->with(
                [
                    "gradeClass",
                    "subject",
                    "branch",
                    'creator',
                    'classrooms'
                ]
            )
            ->withCount('studentsAnswered')
            ->orderByDesc("start_at")
            ->paginate(env("PAGE_LIMIT", 20));

        return $formativeTest;
    }

    public function listGeneralQuizzes(array $data, $query = null)
    {
        $generalQuizzes = GeneralQuiz::query()->notFormative()
            ->with([
                "gradeClass",
                "subject",
                "branch",
                'creator',
                "studentsAnswered" => function ($q) use ($data) {
                    if (isset($data['classroom'])) {
                        $classroomStudents = User::query()->whereHas('student', function ($query) use ($data) {
                            $query->where('classroom_id', $data['classroom']);
                        })->pluck('id')->toArray();
                        $q->whereIn('student_id', $classroomStudents);
                    }
                },
                'classrooms'
            ]);
        if (isset($data['branch_id'])) {
            if (is_array($data['branch_id'])) {
                $generalQuizzes = $generalQuizzes->whereIn("branch_id", $data['branch_id']);
            } else {
                $generalQuizzes = $generalQuizzes->where("branch_id", "=", $data['branch_id']);
            }
        }


        if (isset($data["quiz_type"])) {
            $generalQuizzes = $generalQuizzes->where("quiz_type", "=", $data["quiz_type"]);
        }

        if (isset($data["subject"])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data["subject"]);
        }

        if (isset($data['gradeClass']) and isset($data["quiz_type"]) and $data["quiz_type"] == GeneralQuizTypeEnum::PERIODIC_TEST) {
            $generalQuizzes = $generalQuizzes->where("grade_class_id", '=', $data['gradeClass']);
        }

        if (isset($data['gradeClass']) and isset($data["quiz_type"]) and $data["quiz_type"] != GeneralQuizTypeEnum::PERIODIC_TEST) {
            $generalQuizzes = $generalQuizzes->whereHas("subject", function ($query) use ($data) {
                $query->where('grade_class_id', $data['gradeClass']);
            });
        }

        if (isset($data["classroom"])) {
            $generalQuizzes = $generalQuizzes->whereHas(
                "classrooms",
                function (Builder $classroom) use ($data) {
                    $classroom->where("id", "=", $data['classroom']);
                }
            );
        }

        if (isset($data["from_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "start_at",
                ">=",
                Carbon::parse($data['from_date'])->format("Y-m-d 00:00")
            );
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "end_at",
                "<=",
                Carbon::parse($data['to_date'])->format("Y-m-d 23:59")
            );
        }

        if (isset($data['instructor'])) {
            $generalQuizzes = $generalQuizzes->where("created_by", "=", $data['instructor']);
        }

        if (isset($data['subject'])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data['subject']);
        }
        if (!is_null($query)) {
            return $generalQuizzes;
        }
        return $generalQuizzes->orderByDesc("start_at")->paginate(env("PAGE_LIMIT", 20))->withQueryString();
    }

    public function listGeneralQuizzesWithoutPagination(array $data)
    {
        $generalQuizzes = GeneralQuiz::query()->notFormative();

        if (is_array($data['branch_id'])) {
            $generalQuizzes = $generalQuizzes->whereIn("branch_id", $data['branch_id']);
        } else {
            $generalQuizzes = $generalQuizzes->where("branch_id", "=", $data['branch_id']);
        }

        if (isset($data["quiz_type"])) {
            $generalQuizzes = $generalQuizzes->where("quiz_type", "=", $data["quiz_type"]);
        }

        if (isset($data['gradeClass']) and isset($data["quiz_type"]) and $data["quiz_type"] == GeneralQuizTypeEnum::PERIODIC_TEST) {
            $generalQuizzes = $generalQuizzes->where("grade_class_id", '=', $data['gradeClass']);
        }

        if (isset($data["classroom"])) {
            $generalQuizzes = $generalQuizzes->whereHas(
                "classrooms",
                function (Builder $classroom) use ($data) {
                    $classroom->where("id", "=", $data['classroom']);
                }
            );
        }

        if (isset($data["from_date"])) {
            $generalQuizzes = $generalQuizzes
                ->where("start_at", ">=", Carbon::parse($data['from_date'])->format("Y-m-d 00:00"));
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "end_at",
                "<=",
                Carbon::parse($data['to_date'])->format("Y-m-d 23:59")
            );
        }

        if (isset($data['instructor'])) {
            $generalQuizzes = $generalQuizzes->where("created_by", "=", $data['instructor']);
        }

        if (isset($data['subject'])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data['subject']);
        }

        return $generalQuizzes->orderByDesc("start_at")->get();
    }

    public function getGeneralQuizStudent(GeneralQuiz $generalQuiz)
    {
        return [];
    }


    public function getStudentGeneralQuizPerformance($studentUser, $quizType)
    {
        return GeneralQuizStudent::query()
            ->where('is_finished', 1)
            ->where('student_id', $studentUser->id)
            ->with(['generalQuiz.subject'])
            ->whereHas('generalQuiz', function ($query) use ($quizType) {
                $query->where('end_at', '<', now())
                    ->where('quiz_type', $quizType)
                    ->where('show_result', 1);
            })
            ->get();
    }

    public function getStudentGeneralQuizzesByParent($studentUser, $filters = [])
    {
        $model = $this->applyFilters(new GeneralQuiz(), $filters);
        $model = $model
            ->whereNotNull('published_at')
            ->active();

        $from = request()->has('from') ? request()->from : null;
        $to = request()->has('to') ? request()->to : null;

        if (!is_null($from)) {
            $model = $model->where('start_at', '>', $from);
        } else {
            $model = $model->where('start_at', '<', now());
        }

        if (!is_null($to)) {
            $model = $model->where('end_at', '<', $to);
        }

        return $model->where(
            function ($query) use ($studentUser) {
                $query->whereHas(
                    'students',
                    function ($q) use ($studentUser) {
                        $q->where('id', $studentUser->id);
                    }
                )
                    ->orWhereHas(
                        'classrooms',
                        function ($qu) use ($studentUser) {
                            $qu->where('id', $studentUser->student->classroom_id);
                        }
                    )->whereDoesntHave("students");
            }
        )
            ->orderByDesc("start_at")
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function exportGeneralQuizzes(array $data, $query = null)
    {
        $generalQuizzes = GeneralQuiz::query()
            ->with([
                "gradeClass",
                "subject",
                "branch",
                'creator',
                "studentsAnswered" => function ($q) use ($data) {
                    if (isset($data['classroom'])) {
                        $classroomStudents = User::query()->whereHas('student', function ($query) use ($data) {
                            $query->where('classroom_id', $data['classroom']);
                        })->pluck('id')->toArray();
                        $q->whereIn('student_id', $classroomStudents);
                    }
                },
                'classrooms'
            ]);
        if (isset($data['branch_id'])) {
            if (is_array($data['branch_id'])) {
                $generalQuizzes = $generalQuizzes->whereIn("branch_id", $data['branch_id']);
            } else {
                $generalQuizzes = $generalQuizzes->where("branch_id", "=", $data['branch_id']);
            }
        }


        if (isset($data["quiz_type"])) {
            $generalQuizzes = $generalQuizzes->where("quiz_type", "=", $data["quiz_type"]);
        }

        if (isset($data["subject"])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data["subject"]);
        }

        if (isset($data['gradeClass']) and isset($data["quiz_type"]) and $data["quiz_type"] == GeneralQuizTypeEnum::PERIODIC_TEST) {
            $generalQuizzes = $generalQuizzes->where("grade_class_id", '=', $data['gradeClass']);
        }

        if (isset($data['gradeClass']) and isset($data["quiz_type"]) and $data["quiz_type"] != GeneralQuizTypeEnum::PERIODIC_TEST) {
            $generalQuizzes = $generalQuizzes->whereHas("subject", function ($query) use ($data) {
                $query->where('grade_class_id', $data['gradeClass']);
            });
        }

        if (isset($data["classroom"])) {
            $generalQuizzes = $generalQuizzes->whereHas(
                "classrooms",
                function (Builder $classroom) use ($data) {
                    $classroom->where("id", "=", $data['classroom']);
                }
            );
        }

        if (isset($data["from_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "start_at",
                ">=",
                Carbon::parse($data['from_date'])->format("Y-m-d 00:00")
            );
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "end_at",
                "<=",
                Carbon::parse($data['to_date'])->format("Y-m-d 23:59")
            );
        }

        if (isset($data['instructor'])) {
            $generalQuizzes = $generalQuizzes->where("created_by", "=", $data['instructor']);
        }

        if (isset($data['subject'])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data['subject']);
        }
        if (!is_null($query)) {
            return $generalQuizzes;
        }
        return $generalQuizzes->orderByDesc("start_at")->get();
    }

    public function getGeneralQuizStudentAnswers(GeneralQuiz $generalQuiz)
    {
        return $generalQuiz->studentsAnswered()
            ->with(
                [
                    'user.branch',
                    'user.generalQuizAnswers' => function ($builder) use ($generalQuiz) {
                        $builder->where('general_quiz_id', $generalQuiz->id);
                    }
                ]
            )
            ->get();
    }

    public function listSchoolAdminFormativeTest(array $data, $query = null)
    {
        $generalQuizzes = GeneralQuiz::query()->where('created_by', auth()->user()->id)
            ->where('quiz_type', GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->whereNotNull('published_at')
            ->with(
                [
                    "subject",
                    "branch",
                    'creator',
                    "studentsAnswered"
                ]
            );

        $schoolsAccountID = null;
        if (isset($data["school_id"]) and !isset($data['branch_id'])) {
            $schoolsAccountID = $data["school_id"];
        }
        if (isset($data['branch_id'])) {
            $schoolsAccountID = SchoolAccountBranch::find($data['branch_id'])->school_account_id;
        }
        if (!is_null($schoolsAccountID)) {
            $generalQuizzes->whereHas(
                'gradeClass',
                function ($grade) use ($schoolsAccountID) {
                    $grade->whereHas(
                        "schoolAccounts",
                        function (Builder $schoolAccountQBuilder) use ($schoolsAccountID) {
                            $schoolAccountQBuilder->where("id", $schoolsAccountID);
                        }
                    );
                }
            );
        }

        $generalQuizzes->where(
            function (Builder $innerCondition) use ($data) {
                if (isset($data["from_date"])) {
                    $innerCondition->where(
                        "start_at",
                        ">=",
                        Carbon::parse($data['from_date'])->format("Y-m-d 00:00")
                    );
                }

                if (isset($data["to_date"])) {
                    $innerCondition->where(
                        "start_at",
                        "<=",
                        Carbon::parse($data['to_date'])->format("Y-m-d 23:59")
                    );
                }
            }
        );

        return $generalQuizzes->orderByDesc("start_at")->paginate(env("PAGE_LIMIT", 20))->withQueryString();
    }

    public function listSchoolAdminFormativeTestWithoutPagination(array $data)
    {
        $generalQuizzes = GeneralQuiz::query()->where('created_by', auth()->user()->id)
            ->whereNotNull('published_at')
            ->where('quiz_type',GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->where('start_at', '<=', now());

        $schoolsAccountID = null;
        if (isset($data["school_id"]) and !isset($data['branch_id'])) {
            $schoolsAccountID = $data["school_id"];
        }
        if (isset($data['branch_id'])) {
            $schoolsAccountID = SchoolAccountBranch::find($data['branch_id'])->school_account_id;
        }
        if(!is_null($schoolsAccountID)) {
            $generalQuizzes = $generalQuizzes->whereHas('gradeClass', function ($grade) use ($schoolsAccountID) {
                $grade->whereHas(
                    "schoolAccounts",
                    function (Builder $schoolAccountQBuilder) use ($schoolsAccountID) {
                        $schoolAccountQBuilder->where("id", $schoolsAccountID);
                    }
                );
            });
        }

        if (isset($data["from_date"])) {
            $generalQuizzes = $generalQuizzes
                ->where("start_at", ">=", Carbon::parse($data['from_date'])->format("Y-m-d 00:00"));
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where(
                "end_at",
                "<=",
                Carbon::parse($data['to_date'])->format("Y-m-d 23:59")
            );
        }
        return $generalQuizzes->orderByDesc("start_at")->get();
    }
}
