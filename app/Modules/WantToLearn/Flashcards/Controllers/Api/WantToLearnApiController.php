<?php

namespace App\Modules\WantToLearn\Flashcards\Controllers\Api;

use Illuminate\Support\Facades\Validator;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\WantToLearn\Flashcards\Models\WantToLearnFlashcard;
use App\Modules\WantToLearn\Flashcards\Requests\AddWantToLearnFlashcardRequest;
use App\Modules\WantToLearn\Flashcards\Resources\WantToLearnFlashcardResource;

class WantToLearnApiController extends Controller
{
    public function getMyWantToLearn()
    {
        $user = auth('api')->user();
        $wantToLearn = WantToLearnFlashcard::where('user_id', $user->id)->get();

        return customResponse(WantToLearnFlashcardResource::collection($wantToLearn), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addWantToLearn(int $flashcardId)
    {
        $validator = Validator::make(
            ['flashcard_id' => $flashcardId] ,[
            'flashcard_id' => 'required|exists:course_flashcards,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();
        $wantToLearn = WantToLearnFlashcard::firstOrCreate([
                'flashcard_id' => $flashcardId,
                'user_id'   => $user->id
            ]);
            if($wantToLearn->wasRecentlyCreated)
            return customResponse(null, "Flashcard added successfully", 200, StatusCodesEnum::FAILED);
        else
            return customResponse((object)[], 'Flashcard exists in want to lean list', 400, StatusCodesEnum::DONE);
    }

    public function deleteWantToLearn($flashcardId)
    {
        $validator = Validator::make(
            ['flashcard_id' => $flashcardId] ,[
            'flashcard_id' => 'required|exists:course_flashcards,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();

        $wantToLearn = WantToLearnFlashcard::where(['flashcard_id' => $flashcardId, 'user_id' => $user->id])->first();
        
        if (isset($wantToLearn)){
            $wantToLearn->delete();
            return customResponse((object)[], 'Flashcard deleted successfully', 200, StatusCodesEnum::DONE);
        }
        else{
            return customResponse((object)[], 'Flashcard was not added to want to learn list', 404, StatusCodesEnum::FAILED);
        }
    }
}
