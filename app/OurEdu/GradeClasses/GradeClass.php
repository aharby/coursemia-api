<?php
namespace App\OurEdu\GradeClasses;

use App\Observers\GradeClassObserver;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeColors\Models\GradeColor;
use App\OurEdu\SchoolAccounts\BranchEducationalSystemGradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Users\Models\Student;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class GradeClass extends BaseModel implements Auditable
{
    use SoftDeletes, Translatable, HasFactory;
    use \OwenIt\Auditing\Auditable;


    public $translatedAttributes = [
        'title',
    ];
    protected $fillable = [
        'country_id',
        'educational_system_id',
        'is_active',
        'created_by',
        'our_edu_reference'
    ];

    protected static function boot()
    {
        parent::boot();
        // static::observe(GradeClassObserver::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function educationalSystem() {
        return $this->belongsTo(EducationalSystem::class);
    }

    public function students() {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function packages() {
        return $this->hasMany(Package::class, 'grade_class_id');
    }

    public function branchEducationalSystemGradeClass() {
        return $this->hasMany(BranchEducationalSystemGradeClass::class, 'grade_class_id');
    }

    public function schoolAccounts()
    {
        return $this->belongsToMany(SchoolAccount::class, 'school_account_grade_class');
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.country_id')) {
            if(is_null($this->getOriginal('country_id'))){
                $data['old_values']['country_name'] = '';
            }
            else{
                $data['old_values']['country_name'] = Country::find($this->getOriginal('country_id'))->name;
            }
            $data['new_values']['country_name'] = Country::find($this->getAttribute('country_id'))->name;
        }
        return $data;
    }

    public function gradeColor(){
        return $this->belongsTo(GradeColor::class,'grade_color_id');
    }
}
