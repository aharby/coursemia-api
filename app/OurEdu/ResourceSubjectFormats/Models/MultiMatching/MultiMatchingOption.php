<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultiMatching;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MultiMatchingOption extends BaseModel
{
    use HasFactory;

    protected $table = 'res_multi_matching_options';

    protected $fillable = [
        'option',
        'res_multi_matching_data_id'
    ];

    public function questions()
    {
        return $this->belongsToMany(MultiMatchingQuestion::class, 'res_multi_matching_question_option'  , 'res_multi_matching_option_id' , 'res_multi_matching_question_id');
    }

}
