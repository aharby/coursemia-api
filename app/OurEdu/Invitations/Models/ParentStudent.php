<?php

namespace App\OurEdu\Invitations\Models;

use App\OurEdu\BaseApp\BaseModel;

class ParentStudent extends BaseModel
{
    protected $table = 'parent_student';

    protected $fillable = [
        'student_id',
        'parent_id',
    ];
}
