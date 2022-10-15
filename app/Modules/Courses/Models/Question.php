<?php

namespace App\Modules\Courses\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "questions";

    protected $fillable = [
        'course_id',
        'image',
        'is_active',
        'explanation_image',
        'explanation_voice',
    ];

    protected $translationForeignKey = "question_id";
    protected $translatedAttributes = [
        'title',
        'description',
        'explanation',
    ];

    public function ScopeActive($query)
    {

        $query->where('is_active', '=', 1);
    }

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
    public function getTranslatedDescriptionAttribute()
    {
        return $this->translate(app()->getLocale())->description;
    }
    public function getTranslatedExplanationAttribute()
    {
        return $this->translate(app()->getLocale())->explanation;
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
