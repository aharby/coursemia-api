<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzes\Enums;

class GeneralQuizTypeEnum
{
    public const QUIZ = 'quiz';
    public const HOMEWORK = 'homework';
    public const PERIODIC_TEST = 'periodic_test';
    public const FORMATIVE_TEST = "formative_test";

    public static function getAllQuizTypes()
    {
        return [
            self::QUIZ => trans("quiz." . self::QUIZ),
            self::HOMEWORK => trans("quiz." . self::HOMEWORK),
            self::PERIODIC_TEST => trans("quiz." . self::PERIODIC_TEST),
        ];
    }
}
