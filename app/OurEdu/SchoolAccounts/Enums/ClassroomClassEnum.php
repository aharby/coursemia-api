<?php


namespace App\OurEdu\SchoolAccounts\Enums;


abstract class ClassroomClassEnum
{

    const NOREPEAT = 0;
    const HOURLY = 1;
    const DAILY = 2;
    const WEEKLY = 3;
    const MONTHLY = 4;

    public static function getRepeatBy()
    {
        return [
            self::NOREPEAT => trans("classroomClass.No repeat, Only this session"),
//            self::HOURLY   => trans("classRoomClass.Hourly"),
//            self::DAILY    => trans("classRoomClass.Daily"),
            self::WEEKLY   => trans("classroomClass.Weekly"),
//            self::MONTHLY  => trans("classRoomClass.Monthly")
        ];
    }
}
