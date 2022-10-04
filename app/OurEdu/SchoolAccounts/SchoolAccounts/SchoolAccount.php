<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccounts;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SchoolAccount extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasAttach,SoftDeletes;



    protected static $attachFields = [
        'logo' => [
            'sizes' => ['small/school-accounts/logo' => 'resize,256x144', 'large/school-accounts/logo' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
    ];

    protected $fillable = [
        'name',
        'logo',
        'manager_email',
        'manager_id',
        'school_admin_id',
        'is_active',
        'country_id',
    ];



    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function educationalSystems()
    {
        return $this->belongsToMany(EducationalSystem::class, 'school_account_educational_system');
    }

    public function gradeClasses()
    {
        return $this->belongsToMany(GradeClass::class, 'school_account_grade_class');
    }

    public function educationalTerms()
    {
        return $this->belongsToMany(Option::class, 'educational_term_school_account',
            'school_account_id', 'educational_term_id');
    }

    public function academicYears()
    {
        return $this->belongsToMany(Option::class, 'academic_year_school_account',
            'school_account_id', 'academic_year_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class)->with('translations');
    }

    public function branches()
    {
        return $this->hasMany(SchoolAccountBranch::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function classrooms()
    {
        return $this->hasManyThrough(
            Classroom::class,
            SchoolAccountBranch::class,
            'school_account_id',
            'branch_id'
        );
    }

}
