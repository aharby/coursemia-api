<?php

namespace App\Modules\StaticPages\Models;

use App\Modules\BaseApp\BaseModel;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Users\Models\Student;

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
