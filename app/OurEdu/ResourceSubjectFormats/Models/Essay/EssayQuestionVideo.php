<?php


namespace App\OurEdu\ResourceSubjectFormats\Models\Essay;


use App\OurEdu\BaseApp\BaseModel;

class EssayQuestionVideo extends BaseModel
{
    protected $table = 'essay_video';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_essay_question_id'
    ];
}
