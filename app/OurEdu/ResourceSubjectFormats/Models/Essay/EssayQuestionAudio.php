<?php


namespace App\OurEdu\ResourceSubjectFormats\Models\Essay;


use App\OurEdu\BaseApp\BaseModel;

class EssayQuestionAudio extends BaseModel
{
    protected $table = 'essay_audio';

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
