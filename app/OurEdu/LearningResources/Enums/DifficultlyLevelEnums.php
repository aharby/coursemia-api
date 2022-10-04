<?php


namespace App\OurEdu\LearningResources\Enums;


abstract class DifficultlyLevelEnums
{
    public const
        EASY = 'easy',
        MEDIUM = 'medium',
        DIFFICULT = 'difficult';


    public static function availableDifficultlyLevel()
    {
        return [
            self::EASY => self::EASY,
            self::MEDIUM => self::MEDIUM,
            self::DIFFICULT => self::DIFFICULT,

        ];
    }

    public static function percentageDifficultyLevel($percentage) {
        if ($percentage >= 0 && $percentage <= 30){
            return self::DIFFICULT;
        }
        if ($percentage > 30 && $percentage <= 70){
            return self::MEDIUM;
        }
        if ($percentage > 70){
            return self::EASY;
        }
        return null;
    }

}
