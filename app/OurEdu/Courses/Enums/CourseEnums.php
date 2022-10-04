<?php

namespace App\OurEdu\Courses\Enums;

abstract class CourseEnums
{
    const SUBJECT_COURSE = 'subject_course';
    const PUBLIC_COURSE = 'public_course';
    const TOTAL_STARS = 5;

    /**
     * This constant used seprately
     * in a different page views
     */
    const LIVE_SESSION = 'live_session';

    public static function getTypes()
    {
        return [
            self::SUBJECT_COURSE => self::SUBJECT_COURSE,
            self::PUBLIC_COURSE => self::PUBLIC_COURSE,
        ];
    }

    public static function getFormattedTypes($key = null)
    {
        $types =  [
            self::SUBJECT_COURSE => 'Subject Course',
            self::PUBLIC_COURSE => 'Public Course',
            self::LIVE_SESSION => 'Live Session',
        ];

        return $key ? $types[$key] : $types;
    }
}
