<?php


namespace App\OurEdu\Exams\Enums;


class ExamTypes
{
    public const EXAM = 'exam',
        PRACTICE = 'practice',
        COMPETITION = 'competition',
        INSTRUCTOR_COMPETITION = 'instructor_competition',
        COURSE_COMPETITION =  'course_competition';


    public static function examTypes()
    {
        return [
            self::EXAM => self::EXAM,
            self::PRACTICE => self::PRACTICE,
            self::COMPETITION => self::COMPETITION,
            self::INSTRUCTOR_COMPETITION => self::INSTRUCTOR_COMPETITION,
            self::COURSE_COMPETITION => self::COURSE_COMPETITION,

        ];
    }

}
