<?php
namespace App\OurEdu\SchoolAccounts;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;

class BranchEducationalSystem extends BaseModel
{
    protected $table = 'branch_educational_system';
    protected $fillable = [
        'academic_year_id',
        'educational_term_id',
    ];


    public function gradeClasses()
    {
        return $this->belongsToMany(GradeClass::class, 'branch_educational_system_grade_class','branch_educational_system_id','grade_class_id');
    }

    public function branch()
    {
        return $this->belongsTo(SchoolAccountBranch::class, 'branch_id');
    }

    public function educationalSystem()
    {
        return $this->belongsTo(EducationalSystem::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(Option::class,'academic_year_id');
    }

    public function educationalTerm()
    {
        return $this->belongsTo(Option::class,'educational_term_id');
    }

    public function BranchEducationalSystemGradeClasses(){
        return $this->hasMany(BranchEducationalSystemGradeClass::class,
        'branch_educational_system_id',
        'id'
        );
    }

}
