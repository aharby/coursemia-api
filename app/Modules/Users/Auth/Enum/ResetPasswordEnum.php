<?php

namespace App\Modules\Users\Auth\Enum;

use App\Modules\BaseApp\Enums\DynamicLinksEnum;
use App\Modules\BaseApp\Enums\DynamicLinkTypeEnum;
use App\Modules\Notifications\Enums\NotificationEnum;
use App\Modules\Users\UserEnums;

class ResetPasswordEnum
{
    private $user;
    private $token;
    public $types;

    public function __construct($user,$token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->url = '{firebase_url}/?link={portal_url}/dynamic-link%3F{query_param}&apn=com.Modules.students';

        $this->types = [
            UserEnums::SME_TYPE => env('FIREBASE_URL_PREFIX').'/?link='.env('SME_PORTAL_URL').'/%23/auth/reset-password%3Ftoken%3D'.$this->token.'%26notification_type%3D'.NotificationEnum::FORGOT_PASSWORD.'&apn=com.Modules.students',
            UserEnums::STUDENT_TYPE => env('STUDENT_PORTAL_URL').'/auth/reset-password/'.$this->token,
            UserEnums::STUDENT_TEACHER_TYPE => env('STUDENT_PORTAL_URL').'/auth/reset-password/'.$this->token,
            UserEnums::PARENT_TYPE => env('STUDENT_PORTAL_URL').'/auth/reset-password/'.$this->token,
            UserEnums::ADMIN_TYPE => env('FIREBASE_URL_PREFIX').'/?link='.env('ADMIN_PORTAL_URL').'/auth/update-password%3Ftoken%3D'.$this->token.'%26notification_type%3D'.NotificationEnum::FORGOT_PASSWORD.'&apn=com.Modules.students',
            UserEnums::SUPER_ADMIN_TYPE => env('FIREBASE_URL_PREFIX').'/?link='.env('ADMIN_PORTAL_URL').'/auth/update-password%3Ftoken%3D'.$this->token.'%26notification_type%3D'.NotificationEnum::FORGOT_PASSWORD.'&apn=com.Modules.students',
            UserEnums::INSTRUCTOR_TYPE => env('STUDENT_PORTAL_URL').'/auth/reset-password/'.$this->token,
            UserEnums::SCHOOL_INSTRUCTOR => env('STUDENT_PORTAL_URL').'/auth/reset-password/'.$this->token,
            UserEnums::CONTENT_AUTHOR_TYPE => env('FIREBASE_URL_PREFIX').'/?link='.env('CONTENT_AUTHOR_PORTAL_URL').'/%23/auth/reset-password%3Ftoken%3D'.$this->token.'%26notification_type%3D'.NotificationEnum::FORGOT_PASSWORD.'&apn=com.Modules.students'
        ];
    }

    public function getTypeLink(string $type)
    {
        if(!in_array($type,[UserEnums::SME_TYPE,UserEnums::ADMIN_TYPE,UserEnums::SUPER_ADMIN_TYPE,UserEnums::CONTENT_AUTHOR_TYPE])){
            return getDynamicLink(
                DynamicLinksEnum::STUDENT_DYNAMIC_URL,
                [
                    'firebase_url' => env('FIREBASE_URL_PREFIX'),
                    'portal_url' => env('STUDENT_PORTAL_URL'),
                    'query_param' => 'token%3D'.$this->token.'%26target_screen%3D'.DynamicLinkTypeEnum::RESET_PASSWORD,
                    'android_apn' => env('ANDROID_APN','com.Modules.students')
                ]
            );
        }
        // even if user has no firebase tokens, will be redirected to appstore
        return $this->types[$type];
    }
}
