<?php

namespace App\OurEdu\Subscribes;

use App\OurEdu\BaseApp\BaseModel;

class SubscribeCourse extends BaseModel
{

    public $timestamps = false;

    protected $table = 'course_student';

    protected $fillable =[
        'date_of_pruchase',
        'course_id',
        'instructor_id',
        'student_id',
    ];

}
