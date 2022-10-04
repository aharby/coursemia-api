<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentAnswerDetails extends BaseModel
{
    /**
     * @var array
     */
    protected $table = 'assessment_answer_details';
    protected $fillable = [
        'option_id',
        'option_type',
        'assessment_question_id',
        'assessment_answer_id',
        'user_id',
        'res_question_id',
        'res_question_type',
        'score'
    ];


    public function mainAnswer()
    {

        return $this->belongsTo(AssessmentAnswer::class, 'assessment_answer_id');
    }

    public function optionable()
    {
        return $this->morphTo('option');
    }

    public function assessmentQuestion()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    public function questionable()
    {
        return $this->morphTo("res_question");
    }
}
