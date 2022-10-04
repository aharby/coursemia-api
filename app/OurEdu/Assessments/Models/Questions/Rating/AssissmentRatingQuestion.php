<?php

namespace App\OurEdu\Assessments\Models\Questions\Rating;

use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssissmentRatingQuestion extends BaseModel
{
    use SoftDeletes;
    protected $table = 'assissment_rating_questions';
    protected $guarded = ['id'];

    public function assessmentQuestion()
    {
        return $this->morphOne(AssessmentQuestion::class, "question");
    }

    public function options()
    {
        return $this->hasMany(AssissmentRatingOptions::class, 'assessment_rating_question_id')->orderBy('order','asc');;
    }

}
