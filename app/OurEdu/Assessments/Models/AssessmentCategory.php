<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;

class AssessmentCategory extends BaseModel
{

    protected $fillable = ['title','assessment_id'];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id');
    }

}
