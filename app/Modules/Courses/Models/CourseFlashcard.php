<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFlashcard extends Model
{
    use HasFactory;

    public function getFrontAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["front_$lang"];
    }

    public function getBackAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["back_$lang"];
    }
}
