<?php

namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\BaseApp\Traits\Filterable;
use PDepend\Util\Type;

class GeneralQuizRepository implements GeneralQuizRepositoryInterface
{
    use Filterable;
    public $generalQuiz;

    public function __construct(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
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

    public function saveNewlyGeneralQuizClassrooms(GeneralQuiz $generalQuiz, $classroomIds)
    {
        $generalQuiz->classrooms()->syncWithoutDetaching($classroomIds);
        return $generalQuiz;
    }

    public function getGeneralQuizByGradeClass($gradeClassId){
        return GeneralQuiz::query()
            ->where('grade_class_id', $gradeClassId)
            ->where('quiz_type', GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->where('start_at', '>', now())
            ->active()
            ->get();
    }

    public function getGeneralQuizStudents(GeneralQuiz $generalQuiz, bool $isPaginate = true)
    {
        if ($generalQuiz->students()->count()>0) {
            if ($isPaginate) {
                return $generalQuiz->students()->paginate(request()->input('per_page',null));
            }

            return $generalQuiz->students()->get();
        }
        return $this->students($generalQuiz, $isPaginate);
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

    public function listStudentAvailableGeneralQuizzes(string|array $quizType, array $filters = [])
    {
        $model = $this->applyFilters(new GeneralQuiz(), $filters);
        $data = $model
            ->whereNotNull('published_at')
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now());
             if(is_array($quizType)){
                 $data = $data->whereIn('quiz_type', $quizType);
             }else{
                 $data = $data->where('quiz_type', $quizType);
             }
        $data =  $data->active()
            ->where(
                function ($query){
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

             return $data;
    }

    public function getGeneralQuizQuestions(GeneralQuiz $quiz)
    {
        return $quiz->questions()->with("questions", "sections")->get();
    }

    public function getGeneralQuizQuestionsPaginated(GeneralQuiz $quiz)
    {
        return $quiz->questions()->with("questions", "sections")->paginate(15);
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
            ->jsonPaginate($perPage, ['*','general_quiz_question_bank.question_id'], 'page', $page);

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
            ->jsonPaginate($perPage, ['*','general_quiz_question_bank.question_id'], 'page', $page);
    }

    /**
     * @param GeneralQuiz $generalQuiz
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function students(GeneralQuiz $generalQuiz,$paginate = null)
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
        if(!is_null($paginate)){
            return $students->paginate();
        }
        return $students->get();
    }

    public function updateGeneralQuizMark(GeneralQuiz $generalQuiz)
    {
        $quizMark = $generalQuiz->questions()->pluck('grade')->sum();
        return $generalQuiz->update(['mark' => $quizMark]);
    }

    public function listInstructorGeneralQuizzes(User $instructor, $quizType, $subject_id = null, $gradeClassId = null, $date = null, $report = false, $courseId = null)
    {
        // GeneralQuizTypeEnum::HOMEWORK
        $homeworks = GeneralQuiz::query()
            ->where("quiz_type", "=", $quizType)
            ->where("created_by", "=", $instructor->id);
        if ($subject_id) {
            $homeworks->where("subject_id", "=", $subject_id);
        }

        if ($courseId) {
            $homeworks->where("course_id", "=", $courseId);
        }

        if ($gradeClassId) {
            $homeworks->where("grade_class_id", "=", $gradeClassId);
        }

        if ($date) {
            $date =(new Carbon($date))->format('Y-m-d H:i:s');
            $homeworks->where('start_at', '<=', $date)
                ->where('end_at', '>', $date);
        }

        if ($report) {
            $homeworks = $homeworks->whereNotNull('published_at');
        }

        return $homeworks->orderBy('start_at', 'DESC')
            ->jsonPaginate(request()->input('per_page',null));
    }

    public function listInstructorGeneralQuizzesWithoutPagination(User $instructor, $quizType, $subject_id = null, $gradeClassId = null, $date = null, $report = false, $courseId=null)
    {
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

        if ($courseId) {
            $homeworks->where("course_id", "=", $courseId);
        }

        if ($date) {
            $date =(new Carbon($date))->format('Y-m-d H:i:s');
            $homeworks->where('start_at', '<=', $date)
                ->where('end_at', '>', $date);
        }
        if ($report) {
            $homeworks = $homeworks->whereNotNull('published_at');
        }
        $homeworks=$homeworks->orderBy('start_at', 'DESC')->get();

        return $homeworks;
    }

    public function hasEssayQuestion()
    {
        $count = $this->generalQuiz ->questions()->where('slug', '=', QuestionsTypesEnums::ESSAY)
            ->pluck('id')->count();
        return $count > 0 ? true : false;
    }

    public function listEducationalSupervisorGeneralQuizzes($eduSupervisor, $filters, $classroom = null, $type = null, $instructor = null, $date = null, $paginate = true, array $data = [])
    {

        if (isset($data['branch_id'])) {
            $branches = [$data['branch_id']];
        } else {
            $branches = $eduSupervisor->branches->pluck('id')->toArray() ?? [];
        }

        if (isset($data['subject_id'])) {
            $subjects = [$data['subject_id']];
        } else {
            $subjects = $eduSupervisor->educationalSupervisorSubjects->pluck('id')->toArray();
        }

        if (isset($data['grade_class_id'])) {
            $gradeClasses = [$data['grade_class_id']];
        } else {
            $gradeClasses = $eduSupervisor->educationalSupervisorSubjects->pluck('gradeClass.id')->toArray();
        }

        $generalQuiz = $this->applyFilters(new GeneralQuiz(), $filters)
            ->notFormative()
            ->whereIn('branch_id', $branches)
            ->whereIn('subject_id', $subjects)
            ->whereIn('grade_class_id', $gradeClasses);

        if (!is_null($date)) {
            $date =(new \Carbon\Carbon($date))->format('Y-m-d H:i:s');
            $generalQuiz = $generalQuiz->where('start_at', '>=', $date);
        }

        if (isset($data['from'])) {
            $generalQuiz = $generalQuiz->where("start_at", ">=", Carbon::parse($data['from'])->format("Y-m-d 00:00"));
        }
        if (isset($data['to'])) {
            $generalQuiz = $generalQuiz->where("end_at", "<=", Carbon::parse($data['to'])->format("Y-m-d 23:59"));
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

        if ($paginate) {
            return $generalQuiz->orderByDesc("start_at")->jsonPaginate();
        }
            return $generalQuiz->orderByDesc("start_at")->get();
    }

    public function listGeneralQuizzes(array $data,$query = null)
    {
        $generalQuizzes = GeneralQuiz::query()
            ->notFormative()
            ->with(["gradeClass", "subject", "branch", 'creator',"studentsAnswered"=>function($q) use($data){
                if(isset($data['classroom'])){
                    $classroomStudents = User::query()->whereHas('student',function($query) use($data){
                        $query->where('classroom_id', $data['classroom']);
                    })->pluck('id')->toArray();
                    $q->whereIn('student_id',$classroomStudents);
                }

            },'classrooms']);
        if(isset($data['branch_id'])){
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
            $generalQuizzes = $generalQuizzes->whereHas("subject",function($query) use($data){
                $query->where('grade_class_id',$data['gradeClass']);
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
            $generalQuizzes = $generalQuizzes->where("start_at", ">=", Carbon::parse($data['from_date'])->format("Y-m-d 00:00"));
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where("end_at", "<=", Carbon::parse($data['to_date'])->format("Y-m-d 23:59"));
        }

        if (isset($data['instructor'])) {
            $generalQuizzes = $generalQuizzes->where("created_by", "=", $data['instructor']);
        }

        if (isset($data['subject'])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data['subject']);
        }
        if(!is_null($query)){
            return $generalQuizzes;
        }
        return $generalQuizzes->orderByDesc("start_at")->paginate(env("PAGE_LIMIT", 20))->withQueryString();
    }

    public function trashedClassroomGeneralQuizzes(int $id, bool $paginated = true)
    {
        $classroom = Classroom::onlyTrashed()->find($id);

        $quizzesQuery = $classroom->generalQuizes()
            ->with(["gradeClass", "subject", "branch", "creator"]);

        return $paginated ? $quizzesQuery->paginate() : $quizzesQuery->get();
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
            $generalQuizzes = $generalQuizzes->where("end_at", "<=", Carbon::parse($data['to_date'])->format("Y-m-d 23:59"));
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


    public function getStudentGeneralQuizPerformance($studentUser,$quizType){
        return GeneralQuizStudent::query()
        ->where('is_finished',1)
        ->where('student_id',$studentUser->id)
        ->with(['generalQuiz.subject'])
        ->whereHas('generalQuiz',function($query)use($quizType){
            $query->where('end_at', '<', now())
                ->where('quiz_type', $quizType)
                ->where('show_result',1);
        })
        ->get();
    }

    public function getStudentGeneralQuizzesByParent($studentUser, $filters = [])
    {
        $model = $this->applyFilters(new GeneralQuiz(), $filters);
        $model = $model
            ->where('quiz_type','!=',GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->whereNotNull('published_at')
            ->active();

        $from = request()->has('from')?request()->from:null;
        $to = request()->has('to')?request()->to:null;

        if(!is_null($from)){
            $model = $model->whereDate('start_at','>=',$from);
        }else{
            $model = $model->whereDate('start_at','<=',now());
        }

        if(!is_null($to)){
            $model = $model->whereDate('end_at','<=',$to);
        }

        return $model->with([
            'studentsAnswered'=>
            function ($q) use($studentUser) {
                $q->where('student_id', $studentUser->id);
                $q->where('show_result', true);
                $q->where("is_finished", true);
            }
        ])->whereHas('studentsAnswered',function ($q)use($studentUser) {
            $q->where('student_id', $studentUser->id);
            $q->where('show_result', true);
            $q->where("is_finished", true);
        })
            ->where(
            function ($query) use($studentUser) {
                $query->whereHas(
                    'students',
                    function ($q) use($studentUser) {
                        $q->where('id', $studentUser->id);
                    }
                )
                ->orWhereHas(
                    'classrooms',
                    function ($qu) use($studentUser) {
                        $qu->where('id', $studentUser->student->classroom_id);
                    }
                )->whereDoesntHave("students");
            }
        )
        ->orderByDesc("start_at")
        ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function exportGeneralQuizzes(array $data,$query = null)
    {
        $generalQuizzes = GeneralQuiz::query()
            ->with(["gradeClass", "subject", "branch", 'creator',"studentsAnswered"=>function($q) use($data){
                if(isset($data['classroom'])){
                    $classroomStudents = User::query()->whereHas('student',function($query) use($data){
                        $query->where('classroom_id', $data['classroom']);
                    })->pluck('id')->toArray();
                    $q->whereIn('student_id',$classroomStudents);
                }

            },'classrooms']);
        if(isset($data['branch_id'])){
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
            $generalQuizzes = $generalQuizzes->whereHas("subject",function($query) use($data){
                $query->where('grade_class_id',$data['gradeClass']);
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
            $generalQuizzes = $generalQuizzes->where("start_at", ">=", Carbon::parse($data['from_date'])->format("Y-m-d 00:00"));
        }

        if (isset($data["to_date"])) {
            $generalQuizzes = $generalQuizzes->where("end_at", "<=", Carbon::parse($data['to_date'])->format("Y-m-d 23:59"));
        }

        if (isset($data['instructor'])) {
            $generalQuizzes = $generalQuizzes->where("created_by", "=", $data['instructor']);
        }

        if (isset($data['subject'])) {
            $generalQuizzes = $generalQuizzes->where("subject_id", "=", $data['subject']);
        }
        if(!is_null($query)){
            return $generalQuizzes;
        }
        return $generalQuizzes->orderByDesc("start_at")->get();
    }

    public function getGeneralQuizStudentAnswers(GeneralQuiz $generalQuiz)
    {
       return $generalQuiz->studentsAnswered()->with(['user' , 'user.generalQuizAnswers' => function($builder) use ($generalQuiz){
            $builder->where('general_quiz_id',$generalQuiz->id);
        }] )->get();
    }

    public function trashedStudents(GeneralQuiz $generalQuiz, $paginate = null)
    {
        $studentsQuery = $generalQuiz->students();
        if ($studentsQuery->count() > 0) {
            return $studentsQuery->paginate();
        }

        $classroomsID = $generalQuiz->classrooms()->onlyTrashed()->pluck("id")->toArray();
        $students = User::onlyTrashed()
            ->whereHas("student", function (Builder $builder) use ($classroomsID) {
                $builder->onlyTrashed()
                    ->whereIn("classroom_id", $classroomsID);
            });

        return $paginate ? $students->paginate() : $students->get();
    }

    public function getCourseHomework($courseId){
       return GeneralQuiz::query()
            ->where('course_id', '=', $courseId)
            ->where('end_at', '>', now())
            ->active()
        ->get();

    }

    public function saveGeneralQuizStudentsSubscribed(GeneralQuiz $generalQuiz, $studentIds)
    {
        $generalQuiz->students()->syncWithoutDetaching($studentIds);
        return $generalQuiz;
    }

    public function getGeneralQuizzesStudents($studentUser, $quizType, $courseId = null)
    {
        $homeworks = GeneralQuiz::query()
            ->select('id','title','start_at','end_at','mark')
            ->whereNotNull('published_at')
            ->where('end_at', '<', now())
            ->where('quiz_type', $quizType)
            ->whereHas("students", function ($query) use ($studentUser) {
                $query->where('user_id', $studentUser->id);
            });

            if ($courseId) {
                $homeworks->where("course_id", "=", $courseId);
            }

            $homeworks = $homeworks->orderBy('start_at', 'DESC')
                 ->jsonPaginate(request("per_page") ?? env('PAGE_LIMIT', 20));
          
       return $homeworks;
    }
}
