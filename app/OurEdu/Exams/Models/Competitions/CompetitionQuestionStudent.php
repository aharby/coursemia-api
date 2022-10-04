<?php

namespace App\OurEdu\Exams\Models\Competitions;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;

class CompetitionQuestionStudent extends BaseModel
{
    protected $table = 'competition_question_student';

    protected $fillable = [
        'exam_id',
        'exam_question_id',
        'student_id',
        'is_correct_answer',
    ];


    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
