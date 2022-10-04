<?php


namespace App\OurEdu\ResourceSubjectFormats\Models\Essay;


use App\OurEdu\BaseApp\BaseModel;

class EssayQuestionMedia extends BaseModel
{
    protected $table = 'essay_media';

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
