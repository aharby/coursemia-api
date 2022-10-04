<?php

namespace App\OurEdu\Exams\Repository\Exam;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionQuestionStudent;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamRepository implements ExamRepositoryInterface
{
    use Filterable;
    private $exam;

    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function create(array $data): ?Exam
    {
        return $this->exam->create($data);
    }

    /**
     * @param int $id
     * @return PrepareExamQuestion|null
     */
    public function findOrFail(int $id): ?Exam
    {
        return $this->exam->findOrFail($id);
    }

    /**
     * @param Exam $exam
     * @param array $data
     * @return Exam|null
     */
    public function update(Exam $exam, array $data): ?Exam
    {
        $exam->update($data);
        return $this->exam->findOrFail($exam->id);
    }

    public function createQuestions($data)
    {
        $this->exam->examQuestions()->create($data);
    }

    public function returnQuestion($page): ?LengthAwarePaginator
    {
        $perPage = ExamQuestion::$questionsPerPage;

        $routeName = 'exams';
        switch ($this->exam->type) {
            case ExamTypes::COMPETITION:
                $routeName = 'competitions';
                $parameterName = 'competitionId';
                break;
            case ExamTypes::PRACTICE:
                $routeName = 'practices';
                $parameterName = 'practiceId';
                break;
            case ExamTypes::EXAM:
                $routeName = 'exams';
                $parameterName = 'examId';
                break;
            case ExamTypes::INSTRUCTOR_COMPETITION:
                $routeName = 'instructorCompetitions';
                $parameterName = 'competitionId';
                break;
            case ExamTypes::COURSE_COMPETITION:
                $routeName = 'competitions';
                $parameterName = 'competitionId';

        }
        $questions = $this->exam->questions()
            ->with('questionable')
            ->with('questionable.options')
            ->paginate($perPage, ['*'], 'page', $page);

        return $questions = $questions->withPath(buildScopeRoute(
            "api.student.{$routeName}.get.next-back-questions",
            [
                $parameterName => $this->exam->id,
                'current_question' => $questions->first()->id ?? null
            ]
        ));
    }

    public function findOrFailExamQuestion($questionId)
    {
        return $this->exam->examQuestions()->findOrFail($questionId);
    }

    public function updateExamQuestion($questionId, $updateData)
    {
        return $this->exam->examQuestions()->where('id', $questionId)
            ->update($updateData);
    }

    public function getQuestion(ExamQuestion $examQuestion)
    {
        return $examQuestion->questionable()->firstOrFail();
    }

    public function insertAnswers($questionId, $answerData, $isCorrectQuestion = null)
    {
        $answerData['student_id'] = $student = auth()->user()->student->id ?? null;

        $question = $this->exam->examQuestions()->findOrFail($questionId);
        return $question->answers()->create($answerData);
    }

    public function insertCompetitionQuestionResult($questionId, $isCorrectQuestion)
    {
        $studentId = auth()->user()->student->id;
        $competitionQuestionStudent = CompetitionQuestionStudent::where(
            [
                'exam_id' => $this->exam->id,
                'exam_question_id' => $questionId,
                'student_id' => $studentId]
        )->first();

        if (!$competitionQuestionStudent) {
            $dataCompetition = [
                'exam_id' => $this->exam->id,
                'exam_question_id' => $questionId,
                'student_id' => $studentId,
                'is_correct_answer' => $isCorrectQuestion

            ];
            $competitionQuestionStudent = CompetitionQuestionStudent::create($dataCompetition);
            if ($isCorrectQuestion) {
                $studentComp = CompetitionStudent::query()
                    ->where('student_id', $studentId)
                    ->where('exam_id', $this->exam->id)
                    ->firstOrFail();
                if (is_null($studentComp->result)) {
                    $studentComp->update(['result' => 1]);
                } else {
                    $studentComp->increment('result');
                }
            }
        }

        return $competitionQuestionStudent;
    }

    public function insertInstructorCompetitionQuestionResult($questionId, $isCorrectQuestion)
    {
        $studentId = auth()->user()->student->id;
        $competitionQuestionStudent = InstructorCompetitionQuestionStudent::where(
            [
                'exam_id' => $this->exam->id,
                'exam_question_id' => $questionId,
                'student_id' => $studentId
            ]
        )->first();

        if (!$competitionQuestionStudent) {
            $dataCompetition = [
                'exam_id' => $this->exam->id,
                'exam_question_id' => $questionId,
                'student_id' => $studentId,
                'is_correct_answer' => $isCorrectQuestion
            ];
            $competitionQuestionStudent = InstructorCompetitionQuestionStudent::create($dataCompetition);
        }
        return $competitionQuestionStudent;
    }

    public function createExamTime(ExamQuestion $examQuestion)
    {
        $examQuestion->examQuestionTimes()->latest()->delete();

        $examQuestion->examQuestionTimes()->create([
            'slug' => $examQuestion->slug,
            'exam_question_id' => $examQuestion->id,
            'question_table_type' => $examQuestion->question_table_type,
            'question_table_id' => $examQuestion->question_table_id,
            'exam_id' => $examQuestion->exam_id,
            'student_id' => $examQuestion->exam->student_id,
            'start' => now(),
        ]);
    }

    public function endLastExamQuestionTime($currentQuestionId)
    {
        $question = ExamQuestion::find($currentQuestionId);
        if(!$question)
            return ;
        if ($question->examQuestionTimes()->exists()) {
            $examQuestionTime = ExamQuestion::find($currentQuestionId)->examQuestionTimes()->latest();
            $examQuestionTime->update([
                'end' => now()
            ]);
        }
    }

    public function endAllOpenQuestions()
    {
        return $this->exam->examQuestionsTimes()->whereNull('end')->update([
            'end' => now()
        ]);
    }

    public function getAllQuestion()
    {
        return $this->exam->examQuestions;
    }

    public function getSumTimeForAllExamQuestion()
    {
        return $this->exam->examQuestions()->sum('time_to_solve');
    }

    public function listPreviousExams($studentId, array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Exam(), $filters);
        $exams = $model->where('student_id', $studentId)
            ->where('type', ExamTypes::EXAM)
            ->where('is_finished', 1)->latest()->jsonPaginate();
        return $exams;
    }

    public function listPractices($studentId, array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Exam(), $filters);
        $exams = $model->where('student_id', $studentId)
            ->where('type', ExamTypes::PRACTICE)
            ->latest()
            ->jsonPaginate();
        return $exams;
    }

    public function listCompetitions($studentId, array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Exam(), $filters);
        $exams = $model->where('student_id', $studentId)
            ->where('is_started',1)
            ->where('type', ExamTypes::COMPETITION)
            ->latest()
            ->jsonPaginate();
        return $exams;
    }


    public function getQuestionCount()
    {
        return $this->exam->examQuestions()->count();
    }

    public function joinCompetition($student)
    {
        return $this->exam->competitionStudents()->syncWithoutDetaching($student);
    }

    public function checkIfStudentInCompetition($studentId)
    {
        return $this->exam->competitionStudents()->where(['student_id' => $studentId])->exists();
    }

    public function checkIfStudentInInstructorCompetition($studentId)
    {
        return $this->exam->instructorCompetitionStudents()->where(['student_id' => $studentId])->exists();
    }

    public function joinInstructorCompetition($studentId)
    {
        return $this->exam->instructorCompetitionStudents()->syncWithoutDetaching($studentId);
    }

    public function competitionStudentsCount()
    {
        return $this->exam->competitionStudents()->count();
    }


    public function getExamType()
    {
        return $this->exam->type;
    }

    public function practicesWithSubjectIds()
    {
        return DB::table('exams')
            ->where('type', ExamTypes::PRACTICE)
            ->whereDate('created_at', date('Y-m-d'))
            ->select('subject_id', DB::raw('count(*) as total'))
            ->groupBy('subject_id')
            ->get();
    }


    public function getStudentGrades(int $subjectId, array $filters = [])
    {
        $model = $this->applyFilters(new Exam(),$filters);
        return $model->where('subject_id',$subjectId)->with(['student' => function ($q) {
                $q->with('user', 'school.translations');
            }
            , 'subject.educationalSystem', 'subject.country', 'subject.gradeClass'])
            ->where('type',ExamTypes::EXAM)
            ->paginate(env('PAGE_LIMIT', 20));
    }

    public function pluckAllStudentExamsResultsOnSubject($studentId, $subjectId):Collection
    {
        return $this->exam
            ->where('type', ExamTypes::EXAM)
            ->where('is_finished', 1)
            ->where('subject_id', $subjectId)
            ->where('student_id', $studentId)
            ->pluck('result');
    }

    public function cloneExam(array $options): Exam
    {
        $examData = $this->exam->toArray();

        $clonedExamData = array_merge($examData , $options);

        return $this->exam->create($clonedExamData);
    }

    public function createChallenge(array $data)
    {

        return $this->exam->challenges()->create($data);
    }


    public function getStudentsSpeedPercentageOrderInSubject($subjectId):array
    {
        $orders = $this->exam->where('subject_id', $subjectId)
            ->where('type', ExamTypes::EXAM)
            ->where('is_finished', 1)
            ->select(DB::raw( 'student_id , AVG(solving_speed_percentage) as speed_average' ))
            ->orderBy('speed_average', 'desc')
            ->groupBy('student_id')
            ->get()->pluck('speed_average', 'student_id')->toArray();

        return $orders;
    }

    public function getAllStudentsExamsCounts($subjectId):array
    {
        $counts = $this->exam->where('subject_id', $subjectId)
            ->where('type', ExamTypes::EXAM)
            ->where('is_finished', 1)
            ->select(DB::raw( 'student_id, COUNT(*) as exams_count' ))
            ->orderBy('exams_count', 'desc')
            ->groupBy('student_id')
            ->get()->pluck('exams_count', 'student_id')->toArray();

        return $counts;
    }

    public function getInstructorCourseCompetitions(User $user, $perPage = null,  $page = null,  $pageName = 'page',)
    {
        return $this->exam->where('creator_id', $user->id)
            ->orderBy('start_time', 'desc')
            ->where('type', ExamTypes::COURSE_COMPETITION)
            ->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function listCourseCompetitions($student,$filters = [])
    {
        $model = $this->applyFilters(new Exam(), $filters);
        $exams = $model->whereHas('courseCompetitionStudents', function ($query) use ($student){
            $query->where('student_id',$student->id);
        })
            ->where('type', ExamTypes::COURSE_COMPETITION)
            ->orderBy('start_time', 'desc')
            ->paginate(env('PAGE_LIMIT', 20));
        return $exams;
    }

    public function getFinishedInstructorCourseCompetitions(User $user, $perPage = null, $page = null, $pageName = 'page')
    {
        return $this->exam->where('creator_id', $user->id)
            ->orderBy('start_time', 'desc')
            ->where('is_finished', true)
            ->where('type', ExamTypes::COURSE_COMPETITION)
            ->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

        public function updateStudentsRankInCompetition(Exam $competition)
    {
        $studentsGroupes = $competition->competitionStudents()->orderByPivot('result', 'DESC')
            ->get()->groupBy('pivot.result');

        $index = 0;
        foreach ($studentsGroupes as $key => $studentsGroup){
            $index++;
            CompetitionStudent::query()
                ->whereIn('student_id',$studentsGroup->modelKeys())
                ->where('exam_id', $competition->id)
                ->update(['rank'=>$index]);
        }
    }
}
