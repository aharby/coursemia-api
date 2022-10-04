<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Progress;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;

use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceProgressStudent extends BaseModel
{
    protected $table = 'resource_progress_student';


    protected $fillable = [
        'student_id',
        'subject_id',
        'subject_format_id',
        'resource_slug',
        'resource_id',
        'points',
        'is_visible'

    ];

}
