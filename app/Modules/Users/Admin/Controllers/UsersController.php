<?php


namespace App\Modules\Users\Admin\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Resources\Admin\CoursesResource;
use App\Modules\Users\Admin\Models\Admin;
use App\Modules\Users\Admin\Resources\AdminResource;
use App\Modules\Users\Admin\Resources\UsersResource;
use App\Modules\Users\Models\UserDevice;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use App\Modules\BaseApp\Controllers\AjaxController;
use App\Modules\Users\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends AjaxController
{
    public function index(){
        $search = request()->q;
        $status = request()->status;
        $verified = request()->verified;
        $users = User::query();
        if (isset($search)){
            $users = $users->where(function ($query) use ($search){
                $query->where('full_name', 'LIKE', '%'.$search.'%')
                    ->orWhereRaw("CONCAT(`country_code`,`phone`) LIKE ?", ['%'.$search.'%'])
                    ->orWhere('email', 'LIKE', '%'.$search.'%');
            });
        }
        if (isset($status)){
            $users = $users->where('is_active', '=', $status);
        }
        if (isset($verified)){
            $users = $users->where('is_verified', '=', $verified);
        }
        $users = $users->sorter();
        $users = $users->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $users->total(),
            'users' => UsersResource::collection($users->items())
        ]);
    }

    public function getUserCourses(Request $request){
        $user = User::find($request->user_id);
        $courses = $user->courses();
        if (isset($request->q)){
            $courses = $courses->where(function ($query) use ($request){
                $query->where('title_en', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('title_ar', 'LIKE', '%'.$request->q.'%');
            });
        }
        $courses = $courses->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $courses->total(),
            'courses' => CoursesResource::collection($courses->items())
        ]);
    }

    public function assignCourseToUser(Request $request){
        $user_id = $request->user_id;
        $course_ids = $request->course_ids;
        foreach ($course_ids as $id){
            $user_course = new CourseUser;
            $user_course->user_id = $user_id;
            $user_course->course_id = $id;
            $user_course->save();
        }
        return response()->json("done");
    }

    public function deleteCourseFromUser(Request $request){
        CourseUser::where(['course_id' => $request->course_id, 'user_id' => $request->user_id])->delete();
        return response()->json("done");
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if ($request->has('is_active')) {
            $user->is_active = $request->get('is_active');
        }
        $user->save();
        return customResponse('', trans('api.Updated Successfully'), 200,StatusCodesEnum::DONE);
    }

    public function show($id){
        $user = User::find($id);
        return customResponse(new UsersResource($user), trans('api.Updated successfully'), 200, StatusCodesEnum::DONE);
    }

    public function deleteDevice($id){
        $device = UserDevice::where('id', $id)->first();
        $user = $device->user;
        $device->delete();
        return customResponse(new UsersResource($user), trans('api.Device deleted successfully'), 200, StatusCodesEnum::DONE);
    }
}
