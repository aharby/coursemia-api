<?php

namespace App\OurEdu\PsychologicalTests\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;

class PsychologicalQuestionTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
