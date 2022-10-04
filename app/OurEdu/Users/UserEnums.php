<?php

namespace App\OurEdu\Users;

abstract class UserEnums
{
    /**
     * List of all user's type used in users table
     */
    public const ADMIN_TYPE = 'admin',
        SUPER_ADMIN_TYPE = 'super_admin',
//        TEACHER_TYPE = 'teacher', //replaced with instructor type
        STUDENT_TYPE = 'student',
        CONTENT_AUTHOR_TYPE = 'content_author',
        INSTRUCTOR_TYPE = 'instructor',
        STUDENT_TEACHER_TYPE = 'student_teacher',
        SME_TYPE = 'sme',
        PARENT_TYPE = 'parent',
        SCHOOL_ACCOUNT_MANAGER = 'school_account_manager',
        SCHOOL_SUPERVISOR = 'school_supervisor',
        SCHOOL_LEADER = 'school_leader',
        SCHOOL_INSTRUCTOR = 'school_instructor';
    const ACADEMIC_COORDINATOR= "academic_coordinator";
    const STUDENT_ADVISOR= "student_advisor";
    const EDUCATIONAL_SUPERVISOR = "educational_supervisor";
    const SCHOOL_SECRETARY = "School_secretary";
    const  ASSESSMENT_MANAGER = "assessment_manager";
    const SCHOOL_ADMIN = "school_admin";

    public static function userTypes()
    {
        return [
            self::ADMIN_TYPE => self::ADMIN_TYPE,
            self::SUPER_ADMIN_TYPE => self::SUPER_ADMIN_TYPE,
            self::INSTRUCTOR_TYPE => self::INSTRUCTOR_TYPE,
            self::STUDENT_TYPE => self::STUDENT_TYPE,

        ];
    }

    public static function availableUserType()
    {
        return [
            self::ADMIN_TYPE => self::ADMIN_TYPE,
            self::SME_TYPE => self::SME_TYPE,
            self::CONTENT_AUTHOR_TYPE => self::CONTENT_AUTHOR_TYPE,
            self::INSTRUCTOR_TYPE => self::INSTRUCTOR_TYPE,
            self::STUDENT_TEACHER_TYPE => self::STUDENT_TEACHER_TYPE,
            self::STUDENT_TYPE => self::STUDENT_TYPE,
            self::ASSESSMENT_MANAGER =>self::ASSESSMENT_MANAGER,
            self::SCHOOL_ADMIN =>self::SCHOOL_ADMIN

        ];
    }

    public static function filterableUserType()
    {
        return [
            self::ADMIN_TYPE => self::ADMIN_TYPE,
            self::SME_TYPE => self::SME_TYPE,
            self::CONTENT_AUTHOR_TYPE => self::CONTENT_AUTHOR_TYPE,
            self::INSTRUCTOR_TYPE => self::INSTRUCTOR_TYPE,
            self::STUDENT_TEACHER_TYPE => self::STUDENT_TEACHER_TYPE,
            self::STUDENT_TYPE => self::STUDENT_TYPE,
            self::PARENT_TYPE => self::PARENT_TYPE,
            self::SCHOOL_ACCOUNT_MANAGER => self::SCHOOL_ACCOUNT_MANAGER,
            self::SCHOOL_SUPERVISOR => self::SCHOOL_SUPERVISOR,
            self::SCHOOL_LEADER => self::SCHOOL_LEADER,
            self::SCHOOL_INSTRUCTOR => self::SCHOOL_INSTRUCTOR,
            self::ACADEMIC_COORDINATOR => self::ACADEMIC_COORDINATOR,
            self::ASSESSMENT_MANAGER =>self::ASSESSMENT_MANAGER,
            self::SCHOOL_ADMIN =>self::SCHOOL_ADMIN
        ];
    }

    public static function assessmentUserTypes()
    {
        return [
            self::SCHOOL_ACCOUNT_MANAGER => trans("app." . self::SCHOOL_ACCOUNT_MANAGER),
            self::SCHOOL_SUPERVISOR => trans("app." . self::SCHOOL_SUPERVISOR),
            self::SCHOOL_LEADER => trans("app." . self::SCHOOL_LEADER),
            self::SCHOOL_INSTRUCTOR => trans("app." . self::SCHOOL_INSTRUCTOR),
            self::ACADEMIC_COORDINATOR => trans("app." . self::ACADEMIC_COORDINATOR),
            self::EDUCATIONAL_SUPERVISOR => trans("school-account-users.Educational Supervisor"),
        ];
    }

    public static function getRegistrableUsers()
    {
        return [
            self::PARENT_TYPE => self::PARENT_TYPE,
            self::STUDENT_TYPE => self::STUDENT_TYPE,
            self::STUDENT_TEACHER_TYPE => self::STUDENT_TEACHER_TYPE
        ];
    }

    public static function userCanLoginThrowAbilities(): array
    {
        return [
            self::STUDENT_TYPE,
            self::PARENT_TYPE,
            self::INSTRUCTOR_TYPE
        ];
    }
    public static function userCanLoginThrowBladeDashboard()
    {
        return [
            self::SCHOOL_SUPERVISOR,
            self::ADMIN_TYPE,
            self::SUPER_ADMIN_TYPE,
            self::SCHOOL_LEADER,
            self::SCHOOL_ACCOUNT_MANAGER,
            self::ACADEMIC_COORDINATOR,
            self::SCHOOL_ADMIN,
            self::SME_TYPE
        ];
    }

    public static function schoolAccountUsers ()
    {
        return [
            self::ACADEMIC_COORDINATOR => trans("school-account-users.Academic Coordinator"),
//            self::STUDENT_ADVISOR =>trans("school-account-users.Student Advisor"),
            self::EDUCATIONAL_SUPERVISOR => trans("school-account-users.Educational Supervisor"),
            self::ASSESSMENT_MANAGER =>trans('app.'.self::ASSESSMENT_MANAGER),
//            self::SCHOOL_SECRETARY => trans("school-account-users.School Secretary")
        ];
    }


    public static function assessmentUsers()
    {
        return [
            self::ACADEMIC_COORDINATOR,
            self::EDUCATIONAL_SUPERVISOR,
            self::SCHOOL_INSTRUCTOR,
            self::SCHOOL_ACCOUNT_MANAGER,
            self::SCHOOL_SUPERVISOR,
            self::SCHOOL_LEADER
        ];
    }
    // mainly made for the instructors who can join VCRs
    public static function instructorsUsersTypes()
    {
        return [
            self::SCHOOL_INSTRUCTOR,
            self::INSTRUCTOR_TYPE,
        ];
    }

    public static function getList()
    {
        return [
            self::ADMIN_TYPE => trans("app." . self::ADMIN_TYPE),
            self::SUPER_ADMIN_TYPE => trans("app." . self::SUPER_ADMIN_TYPE),
            self::STUDENT_TYPE => trans("app." . self::STUDENT_TYPE),
            self::CONTENT_AUTHOR_TYPE => trans("app." . self::CONTENT_AUTHOR_TYPE),
            self::INSTRUCTOR_TYPE => trans("app." . self::INSTRUCTOR_TYPE),
            self::STUDENT_TEACHER_TYPE => trans("app." . self::STUDENT_TEACHER_TYPE),
            self::SME_TYPE => trans("app." . self::SME_TYPE),
            self::PARENT_TYPE => trans("app." . self::PARENT_TYPE),
            self::SCHOOL_ACCOUNT_MANAGER => trans("app." . self::SCHOOL_ACCOUNT_MANAGER),
            self::SCHOOL_SUPERVISOR => trans("app." . self::SCHOOL_SUPERVISOR),
            self::SCHOOL_LEADER => trans("app." . self::SCHOOL_LEADER),
            self::SCHOOL_INSTRUCTOR => trans("app." . self::SCHOOL_INSTRUCTOR),
            self::ACADEMIC_COORDINATOR => trans("app." . self::ACADEMIC_COORDINATOR),
            self::STUDENT_ADVISOR => trans("app." . self::STUDENT_ADVISOR),
            self::SCHOOL_SECRETARY => trans("app." . self::SCHOOL_SECRETARY),
            self::ASSESSMENT_MANAGER=>trans('app.'.self::ASSESSMENT_MANAGER),
            self::SCHOOL_ADMIN =>trans('app.'.self::SCHOOL_ADMIN),
        ];
    }

    public static function allowedUserUsingZoom()
    {
        return [
            self::STUDENT_TYPE ,
            self::SCHOOL_ACCOUNT_MANAGER ,
            self::SCHOOL_SUPERVISOR ,
            self::SCHOOL_LEADER ,
            self::ACADEMIC_COORDINATOR,
            self::EDUCATIONAL_SUPERVISOR,
            self::SCHOOL_ADMIN

        ];
    }

    public static function getLabel($key)
    {
        return array_key_exists($key, self::getList()) ? self::getList()[$key] : " ";
    }
}
