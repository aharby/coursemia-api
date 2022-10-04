<?php

namespace App\OurEdu\Quizzes\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Quizzes\Quiz;

class QuizQuestion extends BaseModel
{
    protected $table = 'quiz_questions';
    protected $fillable = [
        'quiz_id',
        'question_type',
        'question_text',
        'question_grade',
        'time_to_solve',
    ];

    public static $questionsPerPage = 1;

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function options()
    {
        return $this->hasMany(QuizQuestionOption::class, 'quiz_question_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizQuestionAnswer::class, 'question_id');
    }
}
