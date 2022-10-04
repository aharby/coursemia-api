<?php

namespace App\OurEdu\Subscribes;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscribe extends BaseModel
{
    use SoftDeletes ;

    protected $table = 'subject_subscribe_students';

    protected $fillable =[
        'date_of_purchase',
        'subject_id',
        'student_id',
        'subject_progress'
    ];

}
