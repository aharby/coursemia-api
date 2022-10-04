<?php

namespace App\OurEdu\PsychologicalTests\Models\SubModels;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;

class PsychologicalResult extends BaseModel
{
    protected $fillable = [
        'psychological_recomendation_id',
        'psychological_test_id',
        'user_id',
        'percentage',
    ];

    public function test()
    {
        return $this->belongsTo(PsychologicalTest::class, 'psychological_test_id');
    }

    public function recomendation()
    {
        return $this->belongsTo(PsychologicalRecomendation::class, 'psychological_recomendation_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
