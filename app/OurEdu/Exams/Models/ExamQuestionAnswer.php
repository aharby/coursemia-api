<?php


namespace App\OurEdu\Exams\Models;


use App\OurEdu\BaseApp\BaseModel;

class ExamQuestionAnswer extends BaseModel
{
    protected $table = 'exam_question_answers';

    protected $fillable = [
        'option_table_type',
        'option_table_id',
        'answer_text',
        'question_id',
        'is_correct_answer',
        'single_question_type',
        'single_question_id',
        'is_answered',
        'student_id',
    ];

    public function optionable()
    {
        return $this->morphTo('option_table');
    }

    public function questionable()
    {
        return $this->morphTo('single_question');
    }
}
