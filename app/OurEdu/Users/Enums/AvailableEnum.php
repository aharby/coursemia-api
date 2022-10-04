<?php


namespace App\OurEdu\Users\Enums;

use BenSampo\Enum\Enum;


final class AvailableEnum extends Enum
{
    const SUBJECT_LIMIT = 3,
        LIVE_SESSION_LIMIT = 3,
        COURSE_LIMIT = 3;
}
