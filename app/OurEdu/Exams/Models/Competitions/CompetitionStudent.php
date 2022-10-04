<?php

namespace App\OurEdu\Exams\Models\Competitions;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;

class CompetitionStudent extends BaseModel
{

    protected $table = 'competition_student';

    protected $fillable = [
        'exam_id',
        'student_id',
        'result',
        'rank',
        'is_finished'

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
