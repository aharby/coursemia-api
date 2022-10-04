<?php

namespace App\OurEdu\Exams\Enums;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\Users\UserEnums;

class PublishGeneralExamEnum
{
    public $generalExamId;
    public $types;

    public function __construct($generalExamId)
    {
        $this->generalExamId = $generalExamId;

        $this->types = [
            UserEnums::STUDENT_TYPE => getDynamicLink(DynamicLinksEnum::STUDENT_DYNAMIC_URL,
            [
                'firebase_url' => env('FIREBASE_URL_PREFIX'),
                'portal_url' => env('STUDENT_PORTAL_URL'),
                'query_param' =>'exam_id%3D'.$generalExamId.'%26target_screen%3D'.DynamicLinkTypeEnum::GENERAL_EXAM,
                'android_apn' => env('ANDROID_APN','com.ouredu.students')
            ]),
        ];
    }

    public function getTypeLink(string $type)
    {
        return $this->types[$type];
    }
}
