<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Matching;

use App\OurEdu\BaseApp\BaseModel;

class MatchingQuestionAudio extends BaseModel
{
    protected $table = 'matching_question_auido';

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
