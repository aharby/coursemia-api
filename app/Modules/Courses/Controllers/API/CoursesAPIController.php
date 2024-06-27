<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Repository\HostCourseRequestRepositoryInterface;
use App\Modules\Courses\Requests\Api\SubmitHostCourseRequestRequest;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Courses\Resources\API\CourseNoteResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursesAPIController extends Controller
{
    public function __construct(public HostCourseRequestRepositoryInterface $hostCourseRequestRepository)
    {
    }

    public function courses(Request $request)
    {
        $courses = Course::query()->active();
        if (isset($request->query_text)) {
            $courses->where(function ($query) use ($request) {
                $query->where('title_ar', 'LIKE', '%' . $request->query_text . '%')
                    ->orWhere('title_en', 'LIKE', '%' . $request->query_text . '%')
                    ->orWhere('description_en', 'LIKE', '%' . $request->query_text . '%')
                    ->orWhere('description_ar', 'LIKE', '%' . $request->query_text . '%');
            });
        }
        if (isset($request->sort_by)) {
            // Sort by most popular
            if ($request->sort_by == 1) {
                $courses->orderBy('rate', 'DESC');
            } elseif ($request->sort_by == 2) {
                $courses->orderBy('created_at', 'DESC');
            }
        }
        if (isset($request->speciality_ids) && count($request->speciality_ids) > 0){
            $courses->whereIn('speciality_id', $request->speciality_ids);
        }
        $courses = $courses->paginate($request->page_size, ['*'], 'page', $request->page_number);
        return customResponse(new CoursesCollection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }

    public function myCourses()
    {
        $user = auth('api')->user();
        $courses = $user->courses;
        return customResponse(CoursesResource::collection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseById()
    {
        $course = Course::find(request()->course_id);
        return customResponse(new CourseDetailsResource($course), __("Get course details successfully"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseLectures(Request $request)
    {
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        if ($v->fails()) {
            return customResponse((object)[], __($v->errors()->first()), 422, StatusCodesEnum::FAILED);
        }
        $lectures = CourseLecture::query();
        $user = auth('api')->user();
        $isMyCourse = 0;
        if (isset($user))
            $isMyCourse = CourseUser::where(['course_id' => $request->course_id, 'user_id' => $user->id])->count();
        if (isset($request->category_id) && !isset($request->sub_category_ids)) {
            $lectures = $lectures->where(function ($q){
                $q->where('category_id', request()->category_id)
                    ->orWhereHas('category', function ($cat){
                        $cat->whereHas('parent', function ($parent){
                            $parent->where('id', request()->category_id);
                        });
                    });
            });
        }
        if (isset($request->sub_category_ids)){
            $lectures = $lectures->whereIn('category_id', $request->sub_category_ids);
        }
        if ($isMyCourse < 1)
            $lectures = $lectures->where('is_free_content' , '=', 1);
        $lectures = $lectures->where('course_id', $request->course_id)->get();
        return customResponse(CourseLectureResource::collection($lectures), __("Done"), 200, StatusCodesEnum::DONE);
    }

    public function getCourseNotes(Request $request)
    {
        $v = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id'
        ]);
        if ($v->fails()) {
            return customResponse((object)[], __($v->errors()->first()), 422, StatusCodesEnum::FAILED);
        }
        $user = auth('api')->user();
        $isMyCourse = 0;
        if (isset($user))
            $isMyCourse = CourseUser::where(['course_id' => $request->course_id, 'user_id' => $user->id])->count();
        $notes = CourseNote::where('course_id', $request->course_id);
        if (isset($request->category_id) && !isset($request->sub_category_ids)) {
            $notes = $notes->where(function ($q){
                $q->where('category_id', request()->category_id)
                    ->orWhereHas('category', function ($cat){
                        $cat->whereHas('parent', function ($parent){
                            $parent->where('id', request()->category_id);
                        });
                    });
            });
        }
        if (isset($request->sub_category_ids)){
            $notes = $notes->whereIn('category_id', $request->sub_category_ids);
        }
        if ($isMyCourse < 1)
            $notes = $notes->where('is_free_content' , '=', 1);
        $notes = $notes->get();
        return customResponse(CourseNoteResource::collection($notes), __("Done"), 200, StatusCodesEnum::DONE);
    }

    public function submitHostCourseRequest(SubmitHostCourseRequestRequest $request)
    {
        if ($this->hostCourseRequestRepository->create($request->all())) {
            return customResponse('', trans('api.Created Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);

    }
}
