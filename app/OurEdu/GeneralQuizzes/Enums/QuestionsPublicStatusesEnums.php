<?php


namespace App\OurEdu\GeneralQuizzes\Enums;


abstract class QuestionsPublicStatusesEnums
{
    const SCHOOL = 'school',
          BRANCH = 'branch',
          PRIVATE = 'private',
           GRADE = 'grade' ;

    public static function getPublicStatuses(): array
    {
        return [
            self::SCHOOL,
            self::BRANCH,
            self::PRIVATE,
            self::GRADE,
        ];
    }
}
