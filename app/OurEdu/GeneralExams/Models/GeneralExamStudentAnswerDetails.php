<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\GeneralExams\GeneralExam;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\GeneralExams\Models\GeneralExamOption;

class GeneralExamStudentAnswerDetails extends BaseModel
{
    /**
     * @var array
     */
    protected $table = 'general_exam_student_answers_details';
    protected $fillable = [
        'general_exam_option_id',
        'is_correct_answer',
        'question_id',
        'main_answer_id',
        'student_id',
        'single_question_id'
    ];


    public function mainAnswer() {

        return $this->belongsTo(GeneralExamStudentAnswer::class , 'main_answer_id');
    }

}
