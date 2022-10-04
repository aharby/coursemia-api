<?php


namespace App\OurEdu\Schools;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Users\Models\Student;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class School extends BaseModel implements Auditable
{
    use Translatable, SoftDeletes, HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'is_active',
        'address',
        'email',
        'mobile',
        'country_id',
        'educational_system_id'
    ];
    protected $translatedAttributes = [
        'name'
    ];

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function educationalSystem(){
        return $this->belongsTo(EducationalSystem::class,'country_id');
    }

    public function students(){
        return $this->hasMany(Student::class,'school_id');
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
        if (Arr::has($data, 'new_values.educational_system_id')) {
            if(is_null($this->getOriginal('educational_system_id'))){
                $data['old_values']['educational_system_name'] = '';
            }
            else{
                $data['old_values']['educational_system_name'] = EducationalSystem::find($this->getOriginal('educational_system_id'))->name;
            }
            $data['new_values']['educational_system_name'] = EducationalSystem::find($this->getAttribute('educational_system_id'))->name;
        }
        return $data;
    }
}
