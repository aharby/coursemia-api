<?php

namespace App\OurEdu\SubjectPackages;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribedPackage extends BaseModel
{
    use SoftDeletes ;

    protected $table = 'packages_subscribed_students';

    protected $fillable =[
        'date_of_purchase',
        'package_id',
        'student_id',
    ];
}
