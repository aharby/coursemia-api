<?php

namespace App\OurEdu\GeneralQuizzes\Models;

use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralQuizStudentAnswerDetails extends BaseModel
{
    /**
     * @var array
     */
    protected $table = 'generalquiz_student_answer_details';
    protected $fillable = [
        'option_id',
        'option_type',
        'is_correct_answer',
        'question_id',
        'main_answer_id',
        'student_id',
        'single_question_id',
        'single_question_type'
    ];


    public function mainAnswer() {

        return $this->belongsTo(GeneralQuizStudentAnswer::class , 'main_answer_id');
    }

    public function optionable()
    {
        return $this->morphTo('option');
    }

    public function questionable()
    {
        return $this->morphTo('single_question');
    }

}
