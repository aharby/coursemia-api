<?php

namespace App\Observers;

use App\Modules\Courses\Models\Answer;
use App\Modules\Courses\Models\Question;

class AnswerObserver
{
    public function updated(Answer $answer){
        $answers = Answer::where('question_id', $answer->question_id)->get();
        $total_selections = $answers->sum('selection_count');
        foreach ($answers as $answer){
            $answer->choosen_percentage = ($answer->selection_count / $total_selections)*100;
            $answer->save();
        }
    }
}
