<?php

namespace App\Modules\Courses\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseNote extends Model
{
    use HasFactory, Translatable;

    protected $translationForeignKey = "course_note_id";
    protected $translatedAttributes = [
        'title',
    ];
    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
}
