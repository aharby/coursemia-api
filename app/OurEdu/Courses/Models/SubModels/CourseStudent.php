<?php

namespace App\OurEdu\Courses\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\BaseApp\Traits\Ratingable;
use App\OurEdu\Users\UserEnums;

class CourseStudent extends BaseModel
{
    protected $table = 'course_student';
    public $timestamps = false;
    protected $fillable = [
        'course_id',
        'student_id',
        'instructor_id',
        'date_of_pruchase',
    ];
}
