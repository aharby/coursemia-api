<?php


namespace App\OurEdu\Users\Auth\Enum;

use App\OurEdu\Users\UserEnums;

class LoginEnum
{
    public $types = [
        UserEnums::PARENT_TYPE => 'http://ouredu.testenv.tech/api/v1/en/redirctUrlLogin',
        UserEnums::STUDENT_TYPE => 'http://ouredu.testenv.tech/api/v1/en/redirctUrlLogin',
        UserEnums::SME_TYPE => 'http://ouredu.testenv.tech/api/v1/en/auth/redirctUrlLogin',
        UserEnums::CONTENT_AUTHOR_TYPE => 'http://ouredu.testenv.tech/api/v1/en/auth/redirctUrlLogin',
        UserEnums::INSTRUCTOR_TYPE => 'http://ouredu.testenv.tech/api/v1/en/auth/redirctUrlLogin',
        UserEnums::SCHOOL_INSTRUCTOR => 'http://ouredu.testenv.tech/api/v1/en/auth/redirctUrlLogin',
        UserEnums::STUDENT_TEACHER_TYPE => 'http://ouredu.testenv.tech/api/v1/en/auth/redirctUrlLogin',
        UserEnums::EDUCATIONAL_SUPERVISOR => 'http://ouredu.testenv.tech/api/v1/en/redirctUrlLogin',
        UserEnums::ASSESSMENT_MANAGER => 'http://ouredu.testenv.tech/api/v1/en/redirctUrlLogin',
    ];

    public function getTypeLink(string $type)
    {
        return $this->types[$type] ?? "";
    }

    public static function getMobileLoginTypes()
    {
        return [
            UserEnums::PARENT_TYPE,
            UserEnums::STUDENT_TYPE,
            UserEnums::STUDENT_TEACHER_TYPE,
        ];
    }

    public static function getWebSmeLoginTypes()
    {
        return [
            UserEnums::SME_TYPE
        ];
    }

    public static function getWebContentAuthorLoginTypes()
    {
        return [
            UserEnums::CONTENT_AUTHOR_TYPE,
        ];
    }

    public static function getWebStudentLoginTypes()
    {
        return [
            UserEnums::PARENT_TYPE,
            UserEnums::STUDENT_TYPE,
            UserEnums::STUDENT_TEACHER_TYPE,
            UserEnums::INSTRUCTOR_TYPE,
            UserEnums::SCHOOL_INSTRUCTOR,
            UserEnums::EDUCATIONAL_SUPERVISOR,
            UserEnums::ASSESSMENT_MANAGER
        ];
    }

    public static function getAssessmentAppLoginTypes()
    {
        return [
            UserEnums::SCHOOL_ACCOUNT_MANAGER,
            UserEnums::SCHOOL_SUPERVISOR,
            UserEnums::SCHOOL_LEADER,
            UserEnums::ACADEMIC_COORDINATOR,
            UserEnums::EDUCATIONAL_SUPERVISOR,
            UserEnums::SCHOOL_INSTRUCTOR,
        ];
    }
}
