<?php

namespace App\Modules\Questions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function getAnswerAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["answer_$lang"];
    }
}
