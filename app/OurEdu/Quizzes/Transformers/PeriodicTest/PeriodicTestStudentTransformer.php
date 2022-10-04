<?php


namespace App\OurEdu\Quizzes\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Transformers\StudentQuizAnswersTransformer;
use League\Fractal\TransformerAbstract;

class PeriodicTestStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
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
            'quiz_type' => (string) $quiz->quiz_type,
            'start_at' => (string) $quiz->start_at,
            'end_at' => (string) $quiz->end_at,
            'grade_class_name' => (string) $quiz->gradeClass ?$quiz->gradeClass->title :null,

        ];


        if ($studentQuiz->status == QuizStatusEnum::FINISHED){
            $transformedData['quiz_result'] =  $studentQuiz->quiz_result_percentage;
        }

        return $transformedData;
    }

    public function includeActions(StudentQuiz $studentQuiz)
    {
        $actions = [];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeStudentAnswers(StudentQuiz $studentQuiz)
    {
        if ($studentQuiz->status == QuizStatusEnum::FINISHED){
            return $this->item($studentQuiz, new StudentQuizAnswersTransformer(), ResourceTypesEnums::STUDENT_QUIZ_ANSWERS);
        }
    }

}
