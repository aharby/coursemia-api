<?php

namespace App\OurEdu\StaticBlocks;

use App\OurEdu\BaseApp\BaseModel;

class StaticBlockTranslation extends BaseModel {

    public $timestamps = false;

    protected $fillable = [
        'title',
        'body'
    ];

}
