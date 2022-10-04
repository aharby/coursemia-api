<?php

namespace App\OurEdu\Assessments\Models\Questions\Matrix;

use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentMatrixData extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assess_matrix_data';
    protected $guarded = ['id'];

    public function assessmentQuestion()
    {
        return $this->morphOne(AssessmentQuestion::class, "question");
    }

    public function columns()
    {
        return $this->hasMany(AssessmentMatrixColumn::class, 'assess_data_id');
    }

    public function rows()
    {
        return $this->hasMany(AssessmentMatrixRow::class, 'assess_data_id');
    }

    public function options()
    {
        return $this->hasMany(AssessmentMatrixColumn::class, 'assess_data_id');
    }
}
