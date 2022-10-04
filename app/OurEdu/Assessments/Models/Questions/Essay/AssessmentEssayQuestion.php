<?php


namespace App\OurEdu\Assessments\Models\Questions\Essay;


use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentEssayQuestion extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        "question",
        "grade",
        "slug",
    ];

    public function assessmentQuestion()
    {
        return $this->morphOne(AssessmentQuestion::class, "question");
    }
}
