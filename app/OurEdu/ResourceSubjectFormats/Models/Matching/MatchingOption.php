<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Matching;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchingOption extends BaseModel
{
    use HasFactory;

    protected $table = 'res_matching_options';

    protected $fillable = [
        'option',
        'res_matching_question_id',
        'res_matching_data_id'
    ];


}
