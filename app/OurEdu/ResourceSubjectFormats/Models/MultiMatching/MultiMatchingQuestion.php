<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultiMatching;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MultiMatchingQuestion extends BaseModel
{
    use HasFactory;

    protected $table = 'res_multi_matching_questions';

    protected $fillable = [
        'text',
        'res_multi_matching_data_id',
    ];

    public function options()
    {
        return $this->belongsToMany(MultiMatchingOption::class, 'res_multi_matching_question_option', 'res_multi_matching_question_id', 'res_multi_matching_option_id');
    }

    public function media()
    {
        return $this->hasOne(MultiMatchingQuestionMedia::class, 'res_multi_matching_question_id');
    }

    public function audio()
    {
        return $this->hasOne(MultiMatchingQuestionAudio::class, 'res_multi_matching_question_id');
    }

    public function video()
    {
        return $this->hasOne(MultiMatchingQuestionVideo::class, 'res_multi_matching_question_id');

    }


}
