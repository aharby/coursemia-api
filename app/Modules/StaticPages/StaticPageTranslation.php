<?php

namespace App\Modules\StaticPages;

use App\Modules\BaseApp\BaseModel;

class StaticPageTranslation extends BaseModel {

    public $timestamps = false;

    protected $fillable = [
        'title',
        'body'
    ];

}
