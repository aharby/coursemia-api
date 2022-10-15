<?php

namespace App\Modules\Courses\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "answers";

    protected $fillable = [
        'question_id',
        'is_correct',
        'chosen_percentage',
    ];

    protected $translationForeignKey = "answer_id";
    protected $translatedAttributes = [
        'answer',
    ];

    public function getTranslatedAnswerAttribute()
    {
        return $this->translate(app()->getLocale())->answer;
    }
    public function question(){
        return $this->belongsTo(Question::class);
    }
}
