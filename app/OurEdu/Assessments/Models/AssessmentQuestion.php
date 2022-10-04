<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentQuestion extends BaseModel
{
    use SoftDeletes;

    protected $table = 'assessment_questions';
    protected $guarded = ['id'];

    public static $questionsPerPage = 1;

    public function question()
    {
        return $this->morphTo('question');
    }

    public function assessorsAnswers()
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_question_id');
    }

    public function answerDetails()
    {
        return $this->hasManyThrough(
            AssessmentAnswerDetails::class,
            AssessmentAnswer::class,
            'assessment_question_id',
            'assessment_answer_id',
            'id',
            'id'
        );
    }

    public function branchScores()
    {
        return $this->belongsToMany(
            SchoolAccountBranch::class,
            "assessment_question_branch_score",
            "assessment_question_id",
            "branch_id"
        )
            ->withPivot("score")
            ->withTimestamps();
    }

    public function category()
    {
        return $this->BelongsTo(AssessmentCategory::class, 'category_id');
    }

}
