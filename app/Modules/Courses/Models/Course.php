<?php

namespace App\Modules\Courses\Models;

use App\Modules\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public function getTitleAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["title_$lang"];
    }

    public function getDescriptionAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["description_$lang"];
    }

    public function lectures(){
        return $this->hasMany(CourseLecture::class);
    }

    public function notes(){
        return $this->hasMany(CourseNote::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function flashCards(){
        return $this->hasMany(CourseFlashcard::class);
    }
}
