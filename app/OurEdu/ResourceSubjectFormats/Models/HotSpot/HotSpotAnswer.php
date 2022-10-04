<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotSpotAnswer extends BaseModel
{
    use SoftDeletes, HasFactory;
    
    protected $table = 'res_hot_spot_answers';

    protected $fillable = [
        'answer',
        'res_hot_spot_question_id'
    ];

    public function question()
    {
        return $this->hasOne(HotSpotQuestion::class, 'res_hot_spot_question_id');
    }

}
