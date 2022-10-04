<?php

namespace App\OurEdu\StaticPages\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;

class DistinguishedStudent extends BaseModel
{
    protected $table = 'distinguished_students';

    protected $fillable = [
        'student_id',
        'general_exam_id',
        'subject_id',
        'total_correct' ,
        'total_questions' ,
        'virtual_classes'
    ];

    public function student() {

        return $this->belongsTo(Student::class , 'student_id');
    }

    public function subject() {

        return $this->belongsTo(Subject::class , 'subject_id');
    }
}
