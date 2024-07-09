<?php

namespace App\Modules\WantToLearn\Flashcards\Models;

use App\Modules\Courses\Models\CourseFlashcard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantToLearnFlashcard extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'flashcard_id'];

    public function flashcard()
    {
        return $this->belongsTo(CourseFlashcard::class);
    }
}
