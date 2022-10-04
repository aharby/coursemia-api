<?php

namespace App\OurEdu\Exams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionQuestionStudent;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionStudent;
use App\OurEdu\Users\Models\Student;

class ExamChallenge extends BaseModel
{
    protected $table = 'exam_challenges';

    protected $fillable = [
        'exam_id',
        'student_id',
        'related_exam_id',
    ];


    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function relatedExam()
    {
        return $this->belongsTo(Exam::class , 'related_exam_id');
    }
}
