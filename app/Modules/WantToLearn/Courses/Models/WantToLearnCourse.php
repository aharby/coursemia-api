<?php

namespace App\Modules\WantToLearn\Courses\Models;

use App\Modules\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantToLearnCourse extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'course_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
