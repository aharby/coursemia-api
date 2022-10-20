<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseReview;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseReviewsResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseReviewsAPIController extends Controller
{
    public function reviews(Request $request){
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id'
        ]);
        if ($v->fails()){
            return customResponse((object)[], __("Course doesn't exist"), 422, StatusCodesEnum::FAILED);
        }
        $course = Course::find($request->course_id);
        return customResponse(new CourseReviewsResource($course), __("Get reviews successfully"), 200, StatusCodesEnum::DONE);
    }

    public function addCourseReview(Request $request){
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'rate'      => 'required|integer|min:1|max:5',
            'comment'   => 'required'
        ]);
        if ($v->fails()){
            return customResponse((object)[], __($v->errors()->first()), 422, StatusCodesEnum::FAILED);
        }
        $user = auth('api')->user();
        $course = Course::find($request->course_id);
        $check_user_have_the_course = CourseUser::where(['course_id' => $request->course_id, 'user_id' => $user->id])
            ->first();
        if (!isset($check_user_have_the_course)){
            return customResponse((object)[], __("Sorry, you didn't buy this course"), 422, StatusCodesEnum::FAILED);
        }
        $check_user_rated_course_before = CourseReview::where(['user_id' => $user->id, 'course_id' => $course->id])
            ->first();
        if (isset($check_user_rated_course_before)){
            return customResponse((object)[], __("You already rated the course before"), 422, StatusCodesEnum::FAILED);
        }

        $course_review = new CourseReview;
        $course_review->rate = $request->rate;
        $course_review->course_id = $request->course_id;
        $course_review->user_id = $user->id;
        $course_review->comment = $request->comment;
        $course_review->save();
        return customResponse((object)[], __("Done"), 200, StatusCodesEnum::DONE);
    }
}
