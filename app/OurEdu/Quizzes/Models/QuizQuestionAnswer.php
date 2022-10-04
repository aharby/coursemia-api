<?php

namespace App\OurEdu\Quizzes\Models;

use App\OurEdu\BaseApp\BaseModel;

class QuizQuestionAnswer extends BaseModel
{
    protected $table = 'quiz_questions_answers';
    protected $fillable = [
        'student_id',
        'is_correct_answer',
        'is_correct_option',
        'quiz_id',
        'question_id',
        'question_grade',
        'option_id',

    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuizQuestionOption::class, 'option_id');
    }
}
