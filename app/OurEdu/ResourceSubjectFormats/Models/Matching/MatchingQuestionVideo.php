<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Matching;

use App\OurEdu\BaseApp\BaseModel;

class MatchingQuestionVideo extends BaseModel
{
    protected $table = 'matching_question_video';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_matching_question_id'
    ];
}
