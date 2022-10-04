<?php

namespace App\OurEdu\PsychologicalTests\Models\SubModels;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;

class PsychologicalAnswer extends BaseModel
{
    protected $fillable = [
        'user_id',
        'psychological_test_id',
        'psychological_question_id',
        'psychological_option_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function test()
    {
        return $this->belongsTo(PsychologicalTest::class, 'psychological_test_id');
    }

    public function question()
    {
        return $this->belongsTo(PsychologicalQuestion::class, 'psychological_question_id');
    }

    public function option()
    {
        return $this->belongsTo(PsychologicalOption::class, 'psychological_option_id');
    }
}
