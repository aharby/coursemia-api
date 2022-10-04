<?php


namespace App\OurEdu\EducationalSystems;

use App\Observers\EducationalSystemObserver;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Countries\Country;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Schools\School;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class EducationalSystem extends BaseModel implements Auditable
{
    use Translatable,HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $auditExclude = [

    ];
    protected $fillable = [
        'country_id',
        'is_active',
        'our_edu_reference'
    ];

    protected $translatedAttributes = [
        'name'
    ];

    protected static function boot()
    {
        parent::boot();
        // static::observe(EducationalSystemObserver::class);
    }
    public function country() {
        return $this->belongsTo(Country::class , 'country_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class , 'educational_system_id');
    }

    public function schools()
    {
        return $this->hasMany(School::class , 'educational_system_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class , 'educational_system_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class , 'educational_system_id');
    }

    public function schoolAccounts()
    {
        return $this->belongsToMany(SchoolAccount::class, 'school_account_educational_system');
    }

    public function schoolAccountBranches()
    {
        return $this->belongsToMany(SchoolAccountBranch::class, 'branch_educational_system','educational_system_id','branch_id');
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
}
