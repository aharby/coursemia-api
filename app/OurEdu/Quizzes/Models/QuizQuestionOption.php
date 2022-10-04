<?php

namespace App\OurEdu\Quizzes\Models;

use App\OurEdu\BaseApp\BaseModel;

class QuizQuestionOption extends BaseModel
{
    protected $table = 'quiz_questions_options';
    protected $fillable = [
        'option',
        'is_correct_answer',
        'quiz_question_id',
    ];
}
