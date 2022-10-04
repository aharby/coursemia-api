<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\TrueFalse;

use App\OurEdu\BaseApp\BaseModel;

class TrueFalseQuestionVideo extends BaseModel
{
    protected $table = 'true_false_video';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_true_false_question_id'
    ];
}
