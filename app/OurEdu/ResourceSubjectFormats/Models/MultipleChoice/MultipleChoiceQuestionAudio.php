<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;

class MultipleChoiceQuestionAudio extends BaseModel
{
    protected $table = 'multiple_choice_question_audio';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
    ];
}
