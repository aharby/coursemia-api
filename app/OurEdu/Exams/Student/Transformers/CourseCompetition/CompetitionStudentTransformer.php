<?php


namespace App\OurEdu\Exams\Student\Transformers\CourseCompetition;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionQuestionFeedBackTransformer;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class CompetitionStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
        'questions',
    ];
    private $params;

    public function __construct(public Exam $exam, $params = [])
    {
        $this->params = $params;
    }

    public function transform(Student $student)
    {
        $user = $student->user;
        $exam = $this->exam;

        $totalCorrectAnswers = $student->pivot->result ?? 0;

        $examQuestionsCount = $this->params['examQuestionsCount'] ?? $exam->questions()->count();
        $transformerData = [
            'id' => (int)$user->id,
            'name' => (string)$user->name,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
            'total_correct_answers' => $totalCorrectAnswers,
            "time_to_solve" =>  $exam->time_to_solve,
            "result" =>  (string)$examQuestionsCount >0 ? $totalCorrectAnswers .'/'.$examQuestionsCount:0,
            'avg_result' => (string) $examQuestionsCount > 0 ? ($totalCorrectAnswers/$examQuestionsCount) * 100 .'%':'0%',
            'student_rank' => (string) ($student->pivot->is_finished) ? getOrdinal($student->pivot->rank):trans("exam.calculating rank in progress")
        ];

        return $transformerData;
    }



    public function includeQuestions(Student $student)
    {
        $questions = $this->exam->questions;

        $questions->each(function ($question) use ($student) {
            $question->student = $student;
        });

        $params = [
            'is_answer' => true
        ];
        if (isset($this->params['actions'])) {
            $params['actions'] = false;
        }
        return $this->collection($questions, new CompetitionQuestionFeedBackTransformer($params), ResourceTypesEnums::COMPETITION_QUESTION);
    }
}
