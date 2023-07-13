<?php

namespace App\Modules\MyProgress\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\FlashCardAnswer;
use App\Modules\Courses\Models\UserQuestionAnswer;
use App\Modules\MyProgress\Requests\GetMyProgressRequest;
use Carbon\Carbon;

class MyProgressApiController extends Controller
{
    public function getMyProgress(GetMyProgressRequest $request)
    {
        $type = $request->type;
        $user = auth('api')->user();
        if ($type == 'weekly')
        {
            $answers = UserQuestionAnswer::where('user_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->get();
            $flashCards = FlashCardAnswer::where('user_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->get();
            $correct_answers = 0;
            $questions = [];
            foreach ($answers as $answer){
                if (!in_array($answer->question_id, $questions))
                    $questions[] = $answer->question_id;
                if($answer->is_correct)
                    $correct_answers++;
            }
            return customResponse([
                'earned_points'     => $correct_answers,
                'solved_questions'  => count($questions),
                'correct_questions' => $correct_answers,
                'solved_flashcards' => count($flashCards)
            ], 'Done', 200, StatusCodesEnum::DONE);
        }elseif ($type == 'monthly')
        {
            $answers = UserQuestionAnswer::where('user_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->get();
            $flashCards = FlashCardAnswer::where('user_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->get();
            $correct_answers = 0;
            $questions = [];
            foreach ($answers as $answer){
                if (!in_array($answer->question_id, $questions))
                    $questions[] = $answer->question_id;
                if($answer->is_correct)
                    $correct_answers++;
            }
            return customResponse([
                'earned_points'     => $correct_answers,
                'solved_questions'  => count($questions),
                'correct_questions' => $correct_answers,
                'solved_flashcards' => count($flashCards)
            ], 'Done', 200, StatusCodesEnum::DONE);
        }
    }
}
