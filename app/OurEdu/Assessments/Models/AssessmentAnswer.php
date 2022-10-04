<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentAnswer extends BaseModel
{

    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'assessment_id',
        'assessment_question_id',
        'score',
        'assessment_user_id',
        'assessee_id',
        'answer_text',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function details()
    {
        return $this->hasMany(AssessmentAnswerDetails::class, 'assessment_answer_id');
    }

    public function assessmentQuestion()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
    
    public function assessmentUser()
    {
        return $this->belongsTo(AssessmentUser::class, 'assessment_user_id');
    }
}
