<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;

class MultipleChoiceQuestionVideo extends BaseModel
{
    protected $table = 'multiple_choice_question_video';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
    ];
}
