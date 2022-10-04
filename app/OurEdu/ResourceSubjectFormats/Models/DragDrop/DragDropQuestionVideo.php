<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\DragDrop;

use App\OurEdu\BaseApp\BaseModel;

class DragDropQuestionVideo extends BaseModel
{
    protected $table = 'drag_drop_question_video';

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
