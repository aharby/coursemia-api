<?php

namespace App\Modules\WantToLearn\Courses\Controllers\Api;

use Illuminate\Support\Facades\Validator;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\WantToLearn\Courses\Models\WantToLearnCourse;

use App\Modules\WantToLearn\Courses\Resources\WantToLearnCourseResource;

class WantToLearnApiController extends Controller
{
    public function getMyWantToLearn()
    {
        $user = auth('api')->user();

        $wantToLearns = WantToLearnCourse::where('user_id', $user->id)->get();
            
        return customResponse(WantToLearnCourseResource::collection($wantToLearns), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addWantToLearn($courseId)
    {
        $validator = Validator::make(
            ['course_id' => $courseId] ,[
            'course_id' => 'required|exists:courses,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();

        $wantToLearn = WantToLearnCourse::firstOrCreate([
                'course_id' => $courseId,
                'user_id'   => $user->id
            ]);
        
        if($wantToLearn->wasRecentlyCreated)
            return customResponse(null, "Course added successfully", 400, StatusCodesEnum::FAILED);
        else
            return customResponse((object)[], 'Course exists in want to lean list', 200, StatusCodesEnum::DONE);
    }

    public function deleteWantToLearn($courseId)
    {
        $validator = Validator::make(
            ['course_id' => $courseId] ,[
            'course_id' => 'required|exists:courses,id'
        ]);


        if ($validator->fails())
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);

        $user = auth('api')->user();
        
        $wantToLearn = WantToLearnCourse::where(['course_id' => $courseId, 'user_id' => $user->id])->first();
        
        if (isset($wantToLearn)){

            $wantToLearn->delete();

            return customResponse((object)[], 'Course deleted successfully', 200, StatusCodesEnum::DONE);
        }
        else{
            return customResponse((object)[], 'Course was not added to want to learn list', 404, StatusCodesEnum::FAILED);
        }
    }
}
