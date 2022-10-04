<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Model;

class PreparationMediaStudent extends BaseModel
{
    protected $fillable = [
        'student_id',
        'preparation_media_id',
        'viewed_at',
        'downloaded_at',

    ];
}
