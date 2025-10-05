<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    protected $fillable = ['user_id', 'instructor_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function assistsInCourse($courseId)
    {
        return $this->instructor
            ->courses()
            ->where('id', $courseId)
            ->exists();
    }
}
