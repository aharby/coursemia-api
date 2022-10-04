<?php
namespace App\OurEdu\Subjects\Models\SubModels;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectSubscribe extends BaseModel
{
    use SoftDeletes;

    protected $table = 'subject_subscribe_students';
    protected $fillable = [
        'subject_id',
        'student_id',
        'date_of_purchase',
        'subject_progress',
        'subject_progress_percentage',
    ];

}
