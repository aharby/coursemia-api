<?php

namespace App\OurEdu\Quizzes\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Users\Models\Student;

class StudentQuiz extends BaseModel
{
    protected $table = 'student_quiz';
    protected $fillable = [
        'quiz_id',
        'quiz_type',
        'status',
        'student_id',
        'questions_ids',
        'quiz_result_percentage',
        'started_at',
        'finished_at',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
