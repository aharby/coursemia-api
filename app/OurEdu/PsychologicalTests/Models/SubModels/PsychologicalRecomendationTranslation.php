<?php

namespace App\OurEdu\PsychologicalTests\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;

class PsychologicalRecomendationTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'result',
        'recomendation',
    ];
}
