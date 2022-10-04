<?php

namespace App\OurEdu\Quizzes\Enums;


class QuizTypesEnum
{
    public const QUIZ = 'quiz';
    public const HOMEWORK = 'homework';
    public const PERIODIC_TEST = 'periodic_test';

    public static function getAllQuizTypes()
    {
        return [
            self::QUIZ => trans("quiz." . self::QUIZ),
            self::HOMEWORK => trans("quiz." . self::HOMEWORK),
            self::PERIODIC_TEST => trans("quiz." . self::PERIODIC_TEST),
        ];
    }

    public static function getLabel($key)
    {
        return array_key_exists($key, self::getAllQuizTypes()) ? self::getAllQuizTypes()[$key] : "";
    }

    public static function getQuizTypes(): array
    {
        return [
            self::HOMEWORK ,
            self::PERIODIC_TEST ,
        ];
    }
}
