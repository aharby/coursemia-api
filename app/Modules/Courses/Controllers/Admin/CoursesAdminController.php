<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Resources\Admin\CoursesCollection;
use App\Modules\Courses\Resources\Admin\CoursesResource;
use Illuminate\Http\Request;

class CoursesAdminController extends Controller
{
    public function index(Request $request){
        $courses = Course::paginate(1000);
        return customResponse(new CoursesCollection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }

    public function show($id){
        $course = Course::find($id);
        return customResponse(new CoursesResource($course), "Done", 200, StatusCodesEnum::DONE);
    }

    public function update(Request $request, $id){
        $course = Course::find($id);
        if ($request->has('is_active')){
            $course->is_active = $request->is_active;
        }
        if ($request->has('title_en')){
            $course->title_en = $request->title_en;
        }
        if ($request->has('title_ar')){
            $course->title_ar = $request->title_ar;
        }
        if ($request->has('description_en')){
            $course->description_en = $request->description_en;
        }
        if ($request->has('description_ar')){
            $course->description_ar = $request->description_ar;
        }
        if ($request->has('price')){
            $course->price = $request->price;
        }
        if ($request->has('expire_date')){
            $course->expire_date = $request->expire_date;
        }
        if ($request->has('cover_image')){
            $course->cover_image = moveSingleGarbageMedia($request->get('cover_image'), 'courses');
        }
        $course->save();
        return customResponse(null, "Updated successfully", 200, StatusCodesEnum::DONE);
    }
}
