<?php

namespace App\OurEdu\StaticPages;

use App\OurEdu\BaseApp\BaseModel;

class StaticPageTranslation extends BaseModel {

    public $timestamps = false;

    protected $fillable = [
        'title',
        'body'
    ];

}
