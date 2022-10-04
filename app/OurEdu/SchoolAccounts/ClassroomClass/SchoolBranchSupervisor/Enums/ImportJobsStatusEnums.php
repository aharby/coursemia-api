<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums;


final class ImportJobsStatusEnums
{
    const PENDING = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;
    const FAILED = 4;

    public static function getList()
    {
        return [
            self::PENDING => trans("Pending"),
            self::IN_PROGRESS => trans("In Progress"),
            self::COMPLETED => trans("Completed"),
            self::FAILED => trans("Failed"),
        ];
    }

    public static function getLabel($key)
    {
        return array_key_exists($key, self::getList()) ? self::getList()[$key] : "";
    }
}
