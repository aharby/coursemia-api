<?php

namespace App\Modules\Courses\Models;

use App\Modules\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseImage extends Model
{
    use HasFactory;

    public function getImageAttribute(){
        return asset($this->attributes['image']);
    }
}
