<?php

namespace App\OurEdu\Exams\Models\InstructorCompetitions;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;

class InstructorCompetitionStudent extends BaseModel
{

    protected $table = 'instructor_competition_student';

    protected $fillable = [
        'exam_id',
        'student_id',
        'result',
    ];


    public function instructorCompetition()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }



}
