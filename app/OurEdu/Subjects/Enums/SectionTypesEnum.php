<?php

namespace App\OurEdu\Subjects\Enums;

abstract class SectionTypesEnum
{
    const SECTION = 'section';

    public static function getTypes()
    {
        return [
            self::SECTION => self::SECTION,
        ];
    }
}
