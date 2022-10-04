<?php

namespace App\OurEdu\VCRSessions\General\Enums;

abstract class VCRSessionsTypeEnum
{
    const SESSION = 'session';
    const COURSE_SESSION = 'course_session';
    const LIVE_SESSION = 'live_session';
    const VCR_SCHEDULE_SESSION = 'vcr_schedule_session';
    const SCHOOL_SESSION = 'school_session';
    const REQUESTED_LIVE_SESSION = 'requested_live_session';
}
