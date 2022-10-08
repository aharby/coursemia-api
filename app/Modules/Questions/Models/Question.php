<?php

namespace App\Modules\Questions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function answers(){
        return $this->hasMany(Answer::class);
    }

    public function getQuestionAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["question_$lang"];
    }

    public function getExplanationAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["explanation_text_$lang"];
    }
}
