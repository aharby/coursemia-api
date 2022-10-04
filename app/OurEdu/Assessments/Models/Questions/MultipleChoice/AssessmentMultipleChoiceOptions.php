<?php

namespace App\OurEdu\Assessments\Models\Questions\MultipleChoice;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentMultipleChoiceOptions extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assessment_multiple_choice_options';
    protected $guarded = ['id'];

    public function answerDetail()
    {
        return $this->morphOne(AssessmentAnswerDetails::class, 'option');
    }
}
