<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Users\User;

class AssessmentAssessor extends BaseModel
{
    protected $table = 'assessment_assessors';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id')->whereNull('deleted_at');
    }

    public function assessment(){
        return $this->belongsTo(Assessment::class,'assessment_id');
    }
}