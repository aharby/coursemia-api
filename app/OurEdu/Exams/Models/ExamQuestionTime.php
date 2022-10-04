<?php


namespace App\OurEdu\Exams\Models;

use App\OurEdu\BaseApp\BaseModel;

class ExamQuestionTime extends BaseModel
{
    protected $table = 'exam_question_times';

    protected $fillable = [
        'slug',
        'exam_question_id',
        'question_table_type',
        'question_table_id',
        'exam_id',
        'student_id',
        'start',
        'end',
    ];
}
