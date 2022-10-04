<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\GeneralExams\GeneralExam;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\GeneralExams\Models\GeneralExamOption;

class GeneralExamStudentAnswer extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'student_id',
        'general_exam_id',
        'general_exam_question_id',
        'general_exam_option_id',
        'answer_text',
        'is_correct',
        'time_to_solve',
    ];

    public function exam()
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function details() {
        return $this->hasMany(GeneralExamStudentAnswerDetails::class , 'main_answer_id');
    }
}
