<?php

namespace App\OurEdu\GeneralExamReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;

class GeneralExamReportQuestion extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'total_answers',
        'correct_answers',
        'wrong_answers',
        'difficulty_parameter',
        'easy_parameter',
        'stability_parameter',
        'trust_parameter',
        'preference_parameter',
        'general_exam_id',
        'general_exam_question_id',
        'general_exam_report_id',
        'subject_format_subject_id'
    ];

    public function report()
    {
        return $this->belongsTo(GeneralExamReport::class, 'general_exam_report_id');
    }

    public function generalExamQuestion()
    {

        return $this->belongsTo(GeneralExamQuestion::class, 'general_exam_question_id');
    }

    public function generalExam()
    {
        return $this->belongsTo(GeneralExam::class);
    }

    public function scopeIgnored($query)
    {
        return $query->where('is_ignored', 1);
    }

    public function scopeReported($query)
    {
        return $query->where('is_reported', 1);
    }

    public function scopeNotIgnored($query)
    {
        return $query->where('is_ignored', 0);
    }

    public function scopeNotReported($query)
    {
        return $query->where('is_reported', 0);
    }
}
