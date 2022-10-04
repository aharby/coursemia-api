<?php

namespace App\OurEdu\PsychologicalTests\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\BaseApp\Traits\HasAttach;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalAnswer;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalResult;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PsychologicalTest extends BaseModel
{
    use SoftDeletes, CreatedBy, Translatable, HasAttach, HasFactory;

    protected $fillable = [
        'is_active'
    ];

    protected $translatedAttributes = [
        'name',
        'instructions',
    ];

    protected static $attachFields = [
        'picture' => [
            'sizes' => ['small' => 'crop,400x300', 'large' => 'resize,800x600'],
            'path' => 'uploads'
        ],
    ];

    public function questions()
    {
        return $this->hasMany(PsychologicalQuestion::class, 'psychological_test_id');
    }

    public function options()
    {
        return $this->hasMany(PsychologicalOption::class, 'psychological_test_id');
    }

    public function activeOptions()
    {
        return $this->hasMany(PsychologicalOption::class, 'psychological_test_id')->active();
    }

    public function recomendations()
    {
        return $this->hasMany(PsychologicalRecomendation::class, 'psychological_test_id');
    }

    public function answers()
    {
        return $this->hasMany(PsychologicalAnswer::class, 'psychological_test_id');
    }

    public function results()
    {
        return $this->hasMany(PsychologicalResult::class, 'psychological_test_id');
    }
}
