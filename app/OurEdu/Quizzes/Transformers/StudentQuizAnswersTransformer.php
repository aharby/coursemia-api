<?php

namespace App\OurEdu\Quizzes\Transformers;

use App\OurEdu\Quizzes\Enums\QuizQuestionsTypesEnum;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class StudentQuizAnswersTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    protected $student;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(StudentQuiz $studentQuiz)
    {
        $quiz = $studentQuiz->quiz;
        $student = $studentQuiz->student;

        $questionsArray = [];
        foreach ($quiz->questions as $question) {
            $answers = QuizQuestionAnswer::query()
                ->where('question_id', $question->id)
                ->where('student_id', $student->id)
                ->get();
            $answersArray = [];

            foreach ($answers as $answer) {
                $answerArray = [
                    'option' => $answer->option->option,
                    'is_correct_answer' => $answer->is_correct_answer,
                    'option_id' => $answer->option_id,
                ];
                if ($question->question_type == QuizQuestionsTypesEnum::MULTIPLE_CHOICE) {
                    $answerArray['is_correct_option'] = $answer->is_correct_option;
                }
                $answersArray[] = $answerArray;
            }
            $questionsArray[] = [
               'question_id' => $question->id,
               'question_text' => $question->question_text,
               'question_type' => $question->question_type,
               'question_grade' => $question->question_grade,
               'answers' => $answersArray
            ];
        }
        $transformedData = [
            'id' => (int) $studentQuiz->id,
            'questions' => $questionsArray,
        ];

        return $transformedData;
    }

}
