<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers;


use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;

class QuizTransformer extends TransformerAbstract
{
    public function transform(Quiz $quiz)
    {
        return [
            'id' => (int) $quiz->id,
            'quiz_type' => (string) $quiz->quiz_type,
            'parent_quiz_id' =>  $quiz->parent_quiz_id,
            'quiz_time' => (string) $quiz->quiz_time,
            'quiz_title' => $quiz->quiz_title,
        ];
    }

}
