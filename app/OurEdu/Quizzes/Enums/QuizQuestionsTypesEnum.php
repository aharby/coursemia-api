<?php

namespace App\OurEdu\Quizzes\Enums;

class QuizQuestionsTypesEnum
{
    public const TRUE_FALSE = 'true_false';
    public const MULTIPLE_CHOICE = 'multiple_choice';

    public static  function getAllQuizQuestionsTypes()
    {
        return [
            self::TRUE_FALSE => self::TRUE_FALSE,
            self::MULTIPLE_CHOICE => self::MULTIPLE_CHOICE,
        ];
    }
}
