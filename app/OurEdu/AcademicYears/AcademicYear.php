<?php


namespace App\OurEdu\AcademicYears;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicYear extends BaseModel
{
    use Translatable, HasFactory;

    protected $fillable = [
        'country_id',
        'educational_system_id',
        'end_date',
        'is_active',
        'our_edu_reference'
    ];

    protected $translatedAttributes = [
        'name'
    ];

    public function country() {
        return $this->belongsTo(Country::class , 'country_id');
    }

    public function educationalSystem() {
        return $this->belongsTo(EducationalSystem::class , 'educational_system_id');
    }
}
