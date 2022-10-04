<?php


namespace App\OurEdu\Countries;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Schools\School;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Country extends BaseModel implements Auditable
{
    use Translatable,SoftDeletes,HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'is_active' ,
        'currency_code',
        'country_code',
    ];
    protected $translatedAttributes = [
        'name',
        'currency'
    ];

    public function educationalSystems()
    {
        return $this->hasMany(EducationalSystem::class , 'country_id');
    }

    public function users()
    {
        return $this->hasMany(User::class , 'country_id');
    }

    public function gradeClasses()
    {
        return $this->hasMany(GradeClass::class , 'country_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class , 'country_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class , 'country_id');
    }

    public function schools()
    {
        return $this->hasMany(School::class , 'country_id');
    }
}
