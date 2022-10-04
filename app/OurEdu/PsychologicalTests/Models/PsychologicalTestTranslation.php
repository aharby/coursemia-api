<?php

namespace App\OurEdu\PsychologicalTests\Models;

use App\OurEdu\BaseApp\BaseModel;

class PsychologicalTestTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'instructions',
    ];
}
