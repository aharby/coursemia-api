<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Matching;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchingQuestion extends BaseModel
{
    use HasFactory;

    protected $table = 'res_matching_questions';

    protected $fillable = [
        'text',
        'res_matching_data_id',
    ];

    public function options()
    {
        return $this->hasMany(MatchingOption::class, 'res_matching_question_id');
    }

    public function media()
    {
        return $this->hasOne(MatchingQuestionMedia::class, 'res_matching_question_id');
    }

    public function audio()
    {
        return $this->hasOne(MatchingQuestionAudio::class, 'res_matching_question_id');
    }

    public function video()
    {
        return $this->hasOne(MatchingQuestionVideo::class, 'res_matching_question_id');
    }


}
