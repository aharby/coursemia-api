<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\BaseApp\BaseModel;

class GeneralExamQuestionQuestion extends BaseModel
{
    /**
     * @var array
     */
    protected $table = "general_exam_question_questions";
    protected $fillable = [
        'question',
        'general_exam_question_id' ,
        'general_exam_correct_option_id'
    ];


    public function options()
    {
        return $this->hasMany(GeneralExamOption::class , 'general_exam_question_question_id');
    }

    public function question()
    {
        return $this->belongsTo(GeneralExamQuestionQuestion::class);
    }

    public function multiMatchingOptions()
    {
        return $this->belongsToMany(GeneralExamOption::class , 'general_exam_questions_options' , 'general_exam_question_question_id' , 'general_exam_option_id');
    }

    public function media()
    {
        return $this->hasOne(GeneralExamQuestionMedia::class, 'general_exam_q_child_id');
    }
}
