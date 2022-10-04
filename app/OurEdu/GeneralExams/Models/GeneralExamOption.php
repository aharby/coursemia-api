<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\BaseApp\BaseModel;

class GeneralExamOption extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'option',
        'general_exam_question_id' ,
        'general_exam_question_question_id',
        'is_correct' ,
        'is_main_answer',
    ];

    public function question()
    {
        return $this->belongsTo(GeneralExamQuestion::class);
    }

    public function multiMatchingQuestions()
    {
        return $this->belongsToMany(GeneralExamQuestionQuestion::class , 'general_exam_questions_options' , 'general_exam_option_id', 'general_exam_question_question_id');
    }
}
