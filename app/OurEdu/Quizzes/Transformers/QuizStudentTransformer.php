<?php

namespace App\OurEdu\Quizzes\Transformers;

use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class QuizStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'studentAnswers',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;
    protected $quiz;
    protected $student;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(StudentQuiz $studentQuiz)
    {
        $this->quiz = $studentQuiz->quiz;
        $this->student = $studentQuiz->student;
        $transformedData = [
            'id' => (int) $studentQuiz->id,
            'student_name' => (string) $this->student->user->name,
            'quiz_type' => (string) $this->quiz->quiz_type,
            'classroom_name' => (string) $this->quiz->classroom->name,
            'quiz_time' => (string) $this->quiz->quiz_time,
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
