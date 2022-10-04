<?php

namespace App\OurEdu\Assessments\Models\Questions\Matrix;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentMatrixRow extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assess_matrix_rows';
    protected $guarded = ['id'];

    public function answerDetail()
    {
        return $this->morphOne(AssessmentAnswerDetails::class, 'res_question');
    }
}
