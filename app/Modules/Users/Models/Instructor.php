<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Courses\Models\Course;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Assistant;

class Instructor extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function assistants()
    {
        return $this->hasMany(Assistant::class);
    }
}
