<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultiMatching;

use App\OurEdu\BaseApp\BaseModel;

class MultiMatchingQuestionAudio extends BaseModel
{
    protected $table = 'multi_matching_question_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_multi_matching_question_id'
    ];
}
