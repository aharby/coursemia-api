<?php

namespace App\Modules\Courses\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFlashcardTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'front',
        'back',
    ];
}
