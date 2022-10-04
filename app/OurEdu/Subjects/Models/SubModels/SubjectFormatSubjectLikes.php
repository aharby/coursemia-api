<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectFormatSubjectLikes extends BaseModel
{
    protected $table = 'subject_format_likes';

    protected $fillable = [
        'user_id',
        'subject_format_subject_id',
    ];

}
