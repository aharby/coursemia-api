<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Users\User;

class AssessmentUser extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assessment_users';
    protected $guarded = ['id'];

    /**
     * Scope a query to only include finished assessments.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where("is_finished", 1);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assessee()
    {
        return $this->belongsTo(User::class, 'assessee_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_user_id');
    }
}
