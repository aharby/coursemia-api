<?php

namespace App\OurEdu\BaseApp\Enums;

abstract class ParentEnum
{
    const ADMIN = 'admin',
        TEACHER = 'teacher',
        STUDENT = 'student',
        SME = 'sme',
        SCHOOL_ACCOUNT_MANAGER = 'school_account_manager',
        SCHOOL_SUPERVISOR = 'school_supervisor',
        SCHOOL_ADMIN = 'school_admin'
    ;
}
