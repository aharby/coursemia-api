<?php
namespace App\OurEdu\Subjects\Models\SubModels;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectFormat extends BaseModel
{
    use SoftDeletes,CreatedBy;

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
    ];

}
