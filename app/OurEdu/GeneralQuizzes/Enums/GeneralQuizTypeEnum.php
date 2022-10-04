<?php

namespace App\OurEdu\GeneralQuizzes\Enums;

class GeneralQuizTypeEnum
{
    public const QUIZ = 'quiz';
    public const HOMEWORK = 'homework';
    public const PERIODIC_TEST = 'periodic_test';
    public const FORMATIVE_TEST = "formative_test";
    public const COURSE_HOMEWORK = "course_homework";

    public static function getAllQuizTypes(): array
    {
        return [
            self::QUIZ => trans("quiz." . self::QUIZ),
            self::HOMEWORK => trans("quiz." . self::HOMEWORK),
            self::PERIODIC_TEST => trans("quiz." . self::PERIODIC_TEST),
            self::COURSE_HOMEWORK => trans("quiz." . self::HOMEWORK),
        ];
    }

    public static function studentShowResultFilters(): array
    {
        return [
            self::HOMEWORK => trans("quiz." . self::HOMEWORK),
            self::PERIODIC_TEST => trans("quiz." . self::PERIODIC_TEST),
        ];
    }

    public static function getLabel(string $key): string
    {
        return array_key_exists($key, self::getAllQuizTypes()) ? self::getAllQuizTypes()[$key] : "";
    }
}
