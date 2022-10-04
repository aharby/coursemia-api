<?php

namespace App\OurEdu\GeneralExamReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralExams\GeneralExam;

class GeneralExamReport extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'general_exam_id'
    ];

    public function reportQuestion()
    {
        return $this->hasMany(GeneralExamReportQuestion::class);
    }

    public function generalExam() {
        return $this->belongsTo(GeneralExam::class , 'general_exam_id');
    }
}
