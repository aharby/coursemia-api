<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Courses\Resources\API\CourseNoteResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursesAPIController extends Controller
{
    public function courses(Request $request){
        $courses = Course::query();
        if (isset($request->query_text)){
            $courses->where(function ($query) use ($request){
                $query->where('title_ar', 'LIKE', '%'.$request->query_text.'%')
                    ->orWhere('title_en', 'LIKE', '%'.$request->query_text.'%')
                    ->orWhere('description_en', 'LIKE', '%'.$request->query_text.'%')
                    ->orWhere('description_ar', 'LIKE', '%'.$request->query_text.'%');
            });
        }
        if (isset($request->sort_by)){
            // Sort by most popular
            if ($request->sort_by == 1) {
                $courses->orderBy('rate', 'DESC');
            }elseif ($request->sort_by == 2) {
                $courses->orderBy('created_at', 'DESC');
            }
        }
        if (isset($request->speciality_ids)){
            $courses->whereIn('speciality_id', $request->speciality_ids);
        }
        $courses = $courses->paginate($request->page_size, ['*'], 'page', $request->page_number);
        return customResponse(new CoursesCollection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }

    public function myCourses(){
        $user = auth('api')->user();
        $courses = $user->courses;
        return customResponse(CoursesResource::collection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseById(){
        $course = Course::find(request()->course_id);
        return customResponse(new CourseDetailsResource($course), __("Get course details successfully"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseLectures(Request $request){
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        if ($v->fails()){
            return customResponse((object)[], __($v->errors()->first()), 422, StatusCodesEnum::FAILED);
        }
        $lectures = CourseLecture::query();
        $lectures->where('course_id', $request->course_id)->get();
        if (isset($request->category_id)){
            $lectures->where('category_id', '=', $request->category_id);
        }
        return customResponse(CourseLectureResource::collection($lectures), __("Done"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseNotes(Request $request){
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id'
        ]);
        if ($v->fails()){
            return customResponse((object)[], __($v->errors()->first()), 422, StatusCodesEnum::FAILED);
        }
        $lectures = CourseNote::where('course_id', $request->course_id)->get();
        return customResponse(CourseNoteResource::collection($lectures), __("Done"), 200, StatusCodesEnum::DONE);
    }
}
