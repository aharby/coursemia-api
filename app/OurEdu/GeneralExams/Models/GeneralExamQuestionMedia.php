<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\BaseApp\BaseModel;

class GeneralExamQuestionMedia extends BaseModel
{
    /**
     * @var array
     */
    protected $table = "general_exam_questions_media";
    protected $fillable = [
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'general_exam_question_id' ,
    ];

}
