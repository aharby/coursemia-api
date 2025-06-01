<?php

namespace App\Modules\WantToLearn\Lectures\Controllers\Api;

use Illuminate\Support\Facades\Validator;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\WantToLearn\Lectures\Models\WantToLearnLecture;
use App\Modules\WantToLearn\Lectures\Resources\WantToLearnLectureResource;

class WantToLearnApiController extends Controller
{
    public function getMyWantToLearn()
    {
        $user = auth('api')->user();

        $wantToLearns = WantToLearnLecture::where('user_id', $user->id)->get();

        return customResponse(WantToLearnLectureResource::collection($wantToLearns), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addWantToLearn($lectureId)
    {
        $validator = Validator::make(
            ['lecture_id' => $lectureId] ,[
            'lecture_id' => 'required|exists:course_lectures,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();
        $wantToLearn = WantToLearnLecture::firstOrCreate([
                'lecture_id' => $lectureId,
                'user_id'   => $user->id
            ]);

        if($wantToLearn->wasRecentlyCreated)
            return customResponse(null, "Lecture added successfully", 200, StatusCodesEnum::DONE);
        else
            return customResponse((object)[], 'Lecture exists in want to lean list', 400, StatusCodesEnum::FAILED);
    }

    public function deleteWantToLearn($lectureId)
    {
        $validator = Validator::make(
            ['lecture_id' => $lectureId] ,[
            'lecture_id' => 'required|exists:course_lectures,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();

        $wantToLearn = WantToLearnLecture::where(['lecture_id' => $lectureId, 'user_id' => $user->id])->first();
        
        if (isset($wantToLearn)){
            $wantToLearn->delete();
            return customResponse((object)[], 'Lecture deleted successfully', 200, StatusCodesEnum::DONE);
        }
        else{
            return customResponse((object)[], 'Lecture was not added to want to learn list', 404, StatusCodesEnum::FAILED);
        }
    }
}
