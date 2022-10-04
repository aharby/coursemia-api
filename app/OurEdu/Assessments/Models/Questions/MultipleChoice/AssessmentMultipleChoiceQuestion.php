<?php

namespace App\OurEdu\Assessments\Models\Questions\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Assessments\Models\AssessmentQuestion;

class AssessmentMultipleChoiceQuestion extends BaseModel
{
    use SoftDeletes;
    protected $table = 'assessment_multiple_choice_questions';
    protected $guarded = ['id'];

    public function assessmentQuestion()
    {
        return $this->morphOne(AssessmentQuestion::class, "question");
    }

    public function options()
    {
        return $this->hasMany(AssessmentMultipleChoiceOptions::class, 'assessment_mcq_id');
    }

}