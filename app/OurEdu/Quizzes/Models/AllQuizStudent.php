<?php

namespace App\OurEdu\Quizzes\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Users\Models\Student;

class AllQuizStudent extends BaseModel
{
    protected $table = 'all_student_quiz';
    protected $fillable = [
        'quiz_id',
        'quiz_type',
        'status',
        'published_at',
        'taken_at',
        'quiz_result_percentage',
        'student_id',
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
