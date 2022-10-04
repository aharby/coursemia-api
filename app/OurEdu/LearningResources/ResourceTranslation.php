<?php

namespace App\OurEdu\LearningResources;

use App\OurEdu\BaseApp\BaseModel;

class ResourceTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
    ];
}
