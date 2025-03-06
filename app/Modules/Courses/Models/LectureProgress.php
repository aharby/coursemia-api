<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Users\Models\User;


class LectureProgress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_lecture_id', 'last_position'];

}
