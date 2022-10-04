<?php


namespace App\OurEdu\Users\Auth\Enum;

use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;

class ActivateAccountEnum
{
    private $user;
    private $token;
    public $types;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->types = [
            UserEnums::PARENT_TYPE => env('FIREBASE_URL_PREFIX').'/activate?link='.env('STUDENT_PORTAL_URL').'/auth/activate%3Ftoken%3D'. $this->token .'%26notification_type%3D'. NotificationEnum::ACTIVATION_MAIL .'&apn=com.ouredu.students ',
            UserEnums::STUDENT_TYPE => env('FIREBASE_URL_PREFIX').'/activate?link='.env('STUDENT_PORTAL_URL').'auth/activate%3Ftoken%3D'. $this->token .'%26notification_type%3D'. NotificationEnum::ACTIVATION_MAIL .'&apn=com.ouredu.students ',
            UserEnums::STUDENT_TEACHER_TYPE => env('FIREBASE_URL_PREFIX').'/activate?link='.env('STUDENT_PORTAL_URL').'/auth/activate%3Ftoken%3D'. $this->token .'%26notification_type%3D'. NotificationEnum::ACTIVATION_MAIL .'&apn=com.ouredu.students ',
            UserEnums::SCHOOL_ACCOUNT_MANAGER => url('ar/auth/activate-school-account?token='.$this->token),
            UserEnums::SCHOOL_SUPERVISOR => url('ar/auth/activate-school-account?token='.$this->token),
            UserEnums::SCHOOL_LEADER => url('ar/auth/activate-school-account?token='.$this->token),
            UserEnums::SCHOOL_INSTRUCTOR => url('ar/auth/activate-school-account?token='.$this->token),
        ];
    }

    public function getTypeLink(string $type)
    {
        return $this->types[$type];
    }


}
