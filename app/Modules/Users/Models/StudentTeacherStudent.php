<?php

namespace App\Modules\Users\Models;

use App\Modules\BaseApp\BaseModel;
use App\Modules\Subjects\Models\Subject;

class StudentTeacherStudent extends BaseModel
{
    protected $table = 'student_student_teacher';

    protected $fillable = [
        'student_id',
        'student_teacher_id',
        'status'
    ];

    /**
     * Subject related to the relation between teacher & student
     * @return Subject
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_student_subject', 'student_student_teacher_id', 'subject_id');
    }
}
