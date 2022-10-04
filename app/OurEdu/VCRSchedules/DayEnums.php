<?php

namespace App\OurEdu\VCRSchedules;

abstract class DayEnums
{
    public const
        SUNDAY = 'sunday',
        MONDAY = 'monday',
        TUESDAY = 'tuesday',
        WEDNESDAY = 'wednesday',
        THURSDAY = 'thursday'  ,
        FRIDAY = 'friday',
        SATURDAY = 'saturday';

    public static function weekDays()
    {
        return [
            self::SUNDAY => self::SUNDAY,
            self::MONDAY => self::MONDAY,
            self::TUESDAY => self::TUESDAY,
            self::WEDNESDAY => self::WEDNESDAY,
            self::THURSDAY => self::THURSDAY,
            self::FRIDAY => self::FRIDAY,
            self::SATURDAY => self::SATURDAY,
        ];
    }
}
