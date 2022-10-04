<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralExams\GeneralExam;

class GeneralExamStudent extends BaseModel
{
    protected $table = 'general_exam_student';
    /**
     * @var array
     */
    protected $fillable = [
        'student_id',
        'subject_id',
        'general_exam_id',
        'is_finished',
        'finished_time',
        'result'
    ];

    public function exam()
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }
}
