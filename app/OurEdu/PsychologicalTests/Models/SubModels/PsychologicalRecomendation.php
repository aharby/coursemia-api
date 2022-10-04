<?php

namespace App\OurEdu\PsychologicalTests\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PsychologicalRecomendation extends BaseModel
{
    use SoftDeletes, CreatedBy, Translatable, HasFactory;

    protected $fillable = [
        'from',
        'to',
        'psychological_test_id',
        'is_active'
    ];

    protected $translatedAttributes = [
        'result',
        'recomendation',
    ];

    public function test()
    {
        return $this->belongsTo(PsychologicalTest::class, 'psychological_test_id');
    }
}
