<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches;

use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SchoolAccountBranch extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable = [
        'name' ,
        'school_account_id' ,
        'supervisor_email' ,
        'leader_email' ,
        'leader_id' ,
        'supervisor_id' ,
        'is_active' ,
        'sms',
        'meeting_type'
    ];


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getStudentsCountAttribute()
    {
        return Student::query()
            ->whereHas(
                "classroom",
                function (Builder $classroom) {
                    $classroom->where("branch_id", "=", $this->id);
                }
            )->count();
    }

    public function schoolAccount()
    {
        return $this->belongsTo(SchoolAccount::class);
    }


    public function educationalSystems()
    {
        return $this->belongsToMany(EducationalSystem::class, 'branch_educational_system', 'branch_id');
    }

    public function branchEducationalSystem()
    {
        return $this->hasMany(BranchEducationalSystem::class, 'branch_id');
    }

    public function schoolInstructors()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'branch_id');
    }

    public function generalQuizzes()
    {
        return $this->hasMany(GeneralQuiz::class, "branch_id");
    }

    public function assessmentQuestion()
    {
        return $this->belongsToMany(
            AssessmentQuestion::class,
            "assessment_question_branch_score",
            "branch_id",
            "assessment_question_id"
        )
            ->withPivot("score")
            ->withTimestamps();
    }
}
