<?php

namespace App\OurEdu\BaseNotification;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Astrotomic\Translatable\Translatable;

class Sms extends BaseModel {
    use CreatedBy;

    protected $table = "sms";

    protected $fillable = [
        'response',
        'message',
        'mobile',
    ];
}
