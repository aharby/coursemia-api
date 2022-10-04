<?php


namespace App\OurEdu\Quizzes\Transformers\HomeWork;


use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Transformers\StudentQuizAnswersTransformer;
use League\Fractal\TransformerAbstract;

class HomeWorkStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'studentAnswers',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(StudentQuiz $studentQuiz)
    {
        $quiz = $studentQuiz->quiz;
        $student = $studentQuiz->student;
        $transformedData = [
            'id' => (int) $studentQuiz->id,
            'student_name' => (string) $student->user->name,
            'student_id' => (string) $student->id,
            'quiz_type' => (string) $quiz->quiz_type,
            'end_at' => (string) $quiz->end_at,
            'classroom_name' => (string) $quiz->classroom->name,
        ];

        if ($studentQuiz->status == QuizStatusEnum::FINISHED){
            $transformedData['quiz_result_percentage'] =  $studentQuiz->quiz_result_percentage;
        }

        return $transformedData;
    }

    public function includeStudentAnswers(StudentQuiz $studentQuiz)
    {
        if ($studentQuiz->status == QuizStatusEnum::FINISHED){
            return $this->item($studentQuiz, new StudentQuizAnswersTransformer(), ResourceTypesEnums::STUDENT_QUIZ_ANSWERS);
        }
    }

}
