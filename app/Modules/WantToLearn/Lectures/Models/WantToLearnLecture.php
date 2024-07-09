<?php

namespace App\Modules\WantToLearn\Lectures\Models;

use App\Modules\Courses\Models\CourseLecture;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantToLearnLecture extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'lecture_id'];

    public function lecture()
    {
        return $this->belongsTo(CourseLecture::class);
    }
}
