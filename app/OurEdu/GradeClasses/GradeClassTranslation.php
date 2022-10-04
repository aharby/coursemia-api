<?php

namespace App\OurEdu\GradeClasses;

use App\OurEdu\BaseApp\BaseModel;

class GradeClassTranslation extends BaseModel
{
    public $timestamps = false;
    protected $fillable = [
        'title',
    ];

}
