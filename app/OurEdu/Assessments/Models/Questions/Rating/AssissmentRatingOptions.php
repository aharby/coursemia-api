<?php

namespace App\OurEdu\Assessments\Models\Questions\Rating;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssissmentRatingOptions extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assissment_rating_options';
    protected $guarded = ['id'];

    public function answerDetail()
    {
        return $this->morphOne(AssessmentAnswerDetails::class, 'option');
    }
}
