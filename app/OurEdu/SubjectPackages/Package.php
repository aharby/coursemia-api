<?php

namespace App\OurEdu\SubjectPackages;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\Scopes\ActiveScope;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subscribes\Subscription;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Package extends BaseModel implements Auditable
{
    use HasAttach, HasFactory;
    use \OwenIt\Auditing\Auditable;


    protected $table = 'subject_packages';
    protected $auditExclude = [
        'picture',
    ];

    protected static $attachFields = [
        'picture' => [
            'sizes' => ['small/subject-packages' => 'resize,256x144', 'large/subject-packages' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'picture',
        'country_id',
        'grade_class_id',
        'educational_system_id',
        'academical_years_id',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope());
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function educationalSystem()
    {
        return $this->belongsTo(EducationalSystem::class);
    }

    public function academicalYears()
    {
        return $this->belongsTo(Option::class, 'academical_years_id');
    }

    public function gradeClass()
    {
        return $this->belongsTo(GradeClass::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'package_subject', 'package_id', 'subject_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'packages_subscribed_students', 'package_id', 'student_id')->withPivot('date_of_purchase');
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscripable');
    }
    public function transformAudit(array $data): array
    {
//        dd($data);
        if (Arr::has($data, 'new_values.country_id')) {
            if(is_null($this->getOriginal('country_id'))){
                $data['old_values']['country_name'] = '';
            }
            else{
                $data['old_values']['country_name'] = Country::find($this->getOriginal('country_id'))->name;
            }
            $data['new_values']['country_name'] = Country::find($this->getAttribute('country_id'))->name;
        }
        if (Arr::has($data, 'new_values.grade_class_id')) {
            if(is_null($this->getOriginal('grade_class_id'))){
                $data['old_values']['grade_class_name'] = '';
            }
            else{
                $data['old_values']['grade_class_name'] = GradeClass::find($this->getOriginal('grade_class_id'))->title;
            }
            $data['new_values']['grade_class_name'] = GradeClass::find($this->getAttribute('grade_class_id'))->title;
        }
        if (Arr::has($data, 'new_values.educational_system_id')) {
            if(is_null($this->getOriginal('educational_system_id'))){
                $data['old_values']['educational_system_name'] = '';
            }
            else{
                $data['old_values']['educational_system_name'] = EducationalSystem::find($this->getOriginal('educational_system_id'))->name;
            }
            $data['new_values']['educational_system_name'] = EducationalSystem::find($this->getAttribute('educational_system_id'))->name;
        }
        if (Arr::has($data, 'new_values.academical_years_id')) {
            if(is_null($this->getOriginal('academical_years_id'))){
                $data['old_values']['academical_years_name'] = '';
            }
            else{
                $data['old_values']['academical_years_name'] = Option::find($this->getOriginal('academical_years_id'))->title;
            }
            if(!is_null($this->getAttribute('academical_years_id'))){
                $data['new_values']['academical_years_name'] = Option::find($this->getAttribute('academical_years_id'))->title;
            }
        }
        return $data;
    }
}
