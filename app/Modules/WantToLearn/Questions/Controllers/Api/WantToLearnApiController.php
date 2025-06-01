<?php

namespace App\Modules\WantToLearn\Questions\Controllers\Api;

use App\Modules\WantToLearn\Questions\Resources\WantToLearnQuestionResource;
use Illuminate\Support\Facades\Validator;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\WantToLearn\Questions\Models\WantToLearnQuestion;

class WantToLearnApiController extends Controller
{
    public function getMyWantToLearn()
    {
        $user = auth('api')->user();
        $wantToLearns = WantToLearnQuestion::where('user_id', $user->id)->get();

        return customResponse(WantToLearnQuestionResource::collection($wantToLearns), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addWantToLearn($questionId)
    {
        $validator = Validator::make(
            ['question_id' => $questionId] ,[
            'question_id' => 'required|exists:questions,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();
        
        $wantToLearn = WantToLearnQuestion::firstOrCreate([
                'question_id' => $questionId,
                'user_id'   => $user->id
            ]);

        if($wantToLearn->wasRecentlyCreated)
            return customResponse(null, "Question added successfully", 200, StatusCodesEnum::FAILED);
        else
            return customResponse((object)[], 'Question exists in want to lean list', 400, StatusCodesEnum::DONE);

    }

    public function deleteWantToLearn($questionId)
    {
        $validator = Validator::make(
            ['question_id' => $questionId] ,[
            'question_id' => 'required|exists:questions,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();
        $wantToLearn = WantToLearnQuestion::where(['question_id' => $questionId, 'user_id' => $user->id])->first();
        
        if (isset($wantToLearn)){
            $wantToLearn->delete();
            return customResponse((object)[], 'Question deleted successfully', 200, StatusCodesEnum::DONE);
        }else{
            return customResponse((object)[], 'Question was not added to want to learn list', 404, StatusCodesEnum::FAILED);
        }

    }
}
