<?php
namespace App\OurEdu\Subjects\Enums;

abstract class SubjectDirectionsEnum
{
    public const RTL = 'rtl',
    LTR = 'ltr';

    public static function getDirections()
    {
        return [
            self::RTL => self::RTL,
            self::LTR => self::LTR,

        ];
    }
}
