<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Users\User;

class AssessmentAssessee extends BaseModel
{
    protected $table = 'assessment_assessees';
    protected $guarded = ['id'];

    public function assessee(){
        return $this->belongsTo(User::class,'user_id')->whereNull('deleted_at');;
    }

    public function assessment(){
        return $this->belongsTo(Assessment::class,'assessment_id');
    }
}