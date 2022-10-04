<?php

namespace App\OurEdu\Assessments\Models\Questions\Matrix;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentMatrixColumn extends BaseModel
{
    use SoftDeletes;
    protected $table = 'assess_matrix_columns';
    protected $guarded = ['id'];
}
