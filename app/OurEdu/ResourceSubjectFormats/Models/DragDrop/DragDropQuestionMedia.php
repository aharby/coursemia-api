<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\DragDrop;

use App\OurEdu\BaseApp\BaseModel;

class DragDropQuestionMedia extends BaseModel
{
    protected $table = 'drag_drop_question_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_drag_drop_question_id'
    ];
}
