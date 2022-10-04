<?php

namespace App\OurEdu\VCRSchedules;

abstract class VCRSessionEnum
{
    public const
        STARTED = 'started';

// all VCR sessions Enum
    // courses_type session
    public const LIVE_SESSION_SESSION = 'live_session';
    public const COURSE_SESSION_SESSION = 'course_session';
    // requested live session AKA spot live session
    public const REQUESTED_LIVE_SESSION = 'requested_live_session';
    public const SCHOOL_SESSION = 'school_session';
    public const VCR_SCHEDULE_SESSION = 'vcr_schedule_session';
}
