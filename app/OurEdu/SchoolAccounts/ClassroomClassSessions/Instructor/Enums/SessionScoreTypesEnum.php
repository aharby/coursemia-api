<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Enums;


abstract class SessionScoreTypesEnum
{

    const ATTENDANCE_SCORE ="attendance_score";
    const DOWNLOAD_SESSION_MEDIA_SCORE = "download_media_score";
    const VIEW_SESSION_MEDIA_SCORE = "view_media_score";

    public static function getSessionScoreTypes()
    {
        return [
            self::ATTENDANCE_SCORE => self::ATTENDANCE_SCORE,
            self::DOWNLOAD_SESSION_MEDIA_SCORE   =>self::DOWNLOAD_SESSION_MEDIA_SCORE,
            self::VIEW_SESSION_MEDIA_SCORE    => self::VIEW_SESSION_MEDIA_SCORE
        ];
    }
}
