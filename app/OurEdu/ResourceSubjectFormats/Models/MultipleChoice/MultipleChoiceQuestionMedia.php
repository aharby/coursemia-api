<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;

class MultipleChoiceQuestionMedia extends BaseModel
{
    protected $table = 'multiple_choice_question_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_multiple_choice_question_id'
    ];
}
