<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Schools\School;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Model;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;

class Assessment extends BaseModel
{
    use CreatedBy;
    use SoftDeletes;
    protected $table = 'assessments';
    protected $guarded = ['id'];

    public static $relations_to_cascade = ['questions','answers','rates','assessmentUsers','resultViewerTypes'];

    public function getAverageScoreAttribute($value)
    {
        if (
            Auth::user()->type == UserEnums::ASSESSMENT_MANAGER ||
            Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER ||
            Auth::user()->type == UserEnums::SCHOOL_ADMIN
        ) {
            return $value;
        }

        return $this->authResultViewer[0]->pivot->average_score ?? 0.00;
    }

    public function assessees()
    {
        return $this->belongsToMany(User::class, 'assessment_assessees', 'assessment_id', 'user_id')->withTimestamps();
    }

    public function assessors()
    {
        return $this->belongsToMany(User::class, 'assessment_assessors', 'assessment_id', 'user_id')->withPivot(['average_score'])->withTimestamps();
    }

    public function resultViewers()
    {
        return $this->belongsToMany(User::class, 'assessment_result_viewers', 'assessment_id', 'user_id')
            ->withPivot(['average_score', 'total_assesses_count', 'assessed_assesses_count'])
            ->withTimestamps()
            ;
    }

    public function authResultViewer()
    {
        return $this->resultViewers()
            ->where("users.id", "=", Auth::id());
    }

    public function resultViewerTypes()
    {
        return $this->hasMany(AssessmentViewerType::class, 'assessment_id');
    }

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'assessment_id');
    }

    // this relation is added due to soft deletes of assessment
    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_id');
    }

    public function rates()
    {
        return $this->hasMany(AssessmentPointsRate::class, 'assessment_id');
    }

    public function schoolAccount()
    {
        return $this->belongsTo(SchoolAccount::class, "school_account_id");
    }

    public function assessmentUsers()
    {
        return $this->hasMany(AssessmentUser::class, 'assessment_id');
    }

    public function assessmentBranchesScores()
    {
        return $this->belongsToMany(SchoolAccountBranch::class, "assessment_branches_score", "assessment_id", "branch_id")
            ->withPivot("score")
            ->withTimestamps();
    }

    public function categories()
    {
        return $this->hasMany(AssessmentCategory::class, 'assessment_id');
    }
}
