<?php


namespace App\OurEdu\SchoolAccounts;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends BaseModel
{
    use SoftDeletes;
    
    protected $fillable = [
        'name' ,
        'branch_id' ,
        'branch_edu_sys_grade_class_id' ,
        'is_special'
    ];

    public function branchEducationalSystemGradeClass()
    {
        return $this->belongsTo(BranchEducationalSystemGradeClass::class,'branch_edu_sys_grade_class_id');
    }
    public function gradeClass()
    {
        return $this->branchEducationalSystemGradeClass->gradeClass;
    }

    public function classroomClass() {

        return $this->hasMany(ClassroomClass::class  , 'classroom_id');
    }

    public function branch()
    {
        return $this->belongsTo(SchoolAccountBranch::class,'branch_id');
    }

    public function students() {

        return $this->hasMany(Student::class , 'classroom_id');
    }

    public function specialStudents()
    {
        return $this->belongsToMany(Student::class);
    }
    /**
     * @return HasMany
     */
    public function sessions()
    {
        return $this->hasMany(ClassroomClassSession::class);
    }

    public function vcrSessions()
    {
        return $this->hasMany(VCRSession::class , 'classroom_id');
    }

    public function classroomClassSessions()
    {
        return $this->hasMany(ClassroomClassSession::class , 'classroom_id');
    }
    
    public function generalQuizes(){
        return $this->belongsToMany(GeneralQuiz::class,'classroom_general_quiz');
    }

}
