<?php

namespace App\OurEdu\Quizzes\Enums;

class QuizTimesEnum
{
    public const PRE_SESSION = 'pre_session';
    public const AFTER_SESSION = 'after_session';
    public const QUIZ_TOTAL_TIME_IN_SECONDS = 300;

    public static  function getAllQuizTimes()
    {
        return [
            self::PRE_SESSION => trans("quiz.pre session"),
            self::AFTER_SESSION => trans("quiz.after session"),
        ];
    }

    public static  function getQuizTimes()
    {
        return [
            self::PRE_SESSION =>  self::PRE_SESSION,
            self::AFTER_SESSION =>  self::AFTER_SESSION,
        ];
    }

    public static function getLabel($key)
    {
        return array_key_exists($key, self::getAllQuizTimes()) ? self::getAllQuizTimes()[$key] : "";
    }
}
