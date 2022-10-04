<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;

class GeneralQuestionMedia extends BaseModel
{
    protected $table = 'general_question_media';
    
    protected $fillable =[
        'source_filename',
        'filename',
        'size',
        'mime_type',
        'url',
        'extension',
        'general_exam_question_id'
    ];

    public function question()
    {
        return $this->belongsTo(GeneralExamQuestion::class, 'general_exam_question_id');
    }
}
