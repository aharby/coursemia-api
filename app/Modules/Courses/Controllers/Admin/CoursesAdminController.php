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
}
