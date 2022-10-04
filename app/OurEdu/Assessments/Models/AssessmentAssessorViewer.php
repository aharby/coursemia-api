<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;

class AssessmentAssessorViewer extends BaseModel
{
    protected $table = 'assessor_result_viewer_avgscore';

    protected $guarded = ['id'];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
