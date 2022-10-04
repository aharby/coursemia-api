<?php

    namespace App\OurEdu\Courses\Enums;

    use App\OurEdu\Courses\Models\SubModels\CourseSession;

    /**
     * Course Session enums
     */
    class CourseSessionEnums
    {
        const ACTIVE = 'active',
            STARTED = 'started',
            DONE = 'done',
            CANCELED = 'canceled';
        const AVAILABILITY_TIME = 30;

        // temporary link
        public static function getUpdatedSessionUrl(CourseSession $courseSession)
        {
            return env('APP_URL') . '/session_url/' . $courseSession->id;
        }
    }
