<?php

namespace App\Modules\Courses\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use Illuminate\Http\Request;

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
        $user = request()->user();
        $courses = $user->courses;
        return customResponse(CoursesResource::collection($courses), __("Fetched courses successfully"), 200, StatusCodesEnum::DONE);
    }
}
