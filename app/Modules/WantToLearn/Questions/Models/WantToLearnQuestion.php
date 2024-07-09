<?php

namespace App\Modules\WantToLearn\Questions\Models;

use App\Modules\Courses\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantToLearnQuestion extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
