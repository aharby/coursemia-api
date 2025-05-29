<?php

namespace App\Modules\WantToLearn;

use App\Http\Controllers\Controller;

use App\Modules\WantToLearn\Questions\Models\WantToLearnQuestion;
use App\Modules\WantToLearn\Lectures\Models\WantToLearnLecture;
use App\Modules\WantToLearn\Flashcards\Models\WantToLearnFlashcard;

use App\Enums\StatusCodesEnum;

class WantToLearnApiController extends Controller
{
    public function getCount()
    {
        $user = auth('api')->user();
        if (!$user) {
            return customResponse((object)[], 'Unauthorized', 401, StatusCodesEnum::UNAUTHORIZED);
        }

        $lectureCount = WantToLearnLecture::where('user_id', $user->id)->count();
        $flashcardCount = WantToLearnFlashcard::where('user_id', $user->id)->count();
        $questionCount = WantToLearnQuestion::where('user_id', $user->id)->count();
        
        return customResponse([
            'lectures' => $lectureCount,
            'flashcards' => $flashcardCount,
            'questions' => $questionCount,
            'total' => $lectureCount + $flashcardCount + $questionCount
        ], 'Done', 200, StatusCodesEnum::DONE);
    }
}