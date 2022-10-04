<?php
namespace App\OurEdu\SchoolAccounts;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GradeClasses\GradeClass;

class BranchEducationalSystemGradeClass extends BaseModel
{
    protected $table = 'branch_educational_system_grade_class';

    public function branchEducationalSystem()
    {
        return $this->belongsTo(BranchEducationalSystem::class,'branch_educational_system_id');
    }

    public function gradeClass()
    {
        return $this->belongsTo(GradeClass::class,'grade_class_id');
    }

}
