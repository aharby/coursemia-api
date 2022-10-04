<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentPointsRate extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assessment_points_rates';
    protected $guarded = ['id'];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
