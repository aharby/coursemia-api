<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use App\Modules\Payment\Models\CartCourse;

use App\Modules\Courses\Resources\API\CoursesResource;

class CartAPIController extends Controller
{
    public function getCourses()
    {
        $user = auth('api')->user();

        $courses = $user->cartCourses;
    
        return customResponse(CoursesResource::collection($courses), __("Fetched cart courses successfully"), 200, StatusCodesEnum::DONE);

    }

    public function addCourse($courseId)
    {
        $validator = Validator::make(
            ['course_id' => $courseId] ,[
            'course_id' => 'required|exists:courses,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $user = auth('api')->user();

        $courseAlreadyInCart = $user->cartCourses->contains($courseId);

        if($courseAlreadyInCart){
            return customResponse(null, "Course already in cart", 400, StatusCodesEnum::FAILED);
        }

        $user->cartCourses->create([
            'course_id'=> $courseId
        ]);

        return customResponse(null, "Course added to cart", 200, StatusCodesEnum::DONE);

    }

    public function removeCourse($courseId)
    {
        $validator = Validator::make(
            ['course_id' => $courseId] ,[
            'course_id' => 'required|exists:courses,id'
        ]);


        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }


        $userId = auth('api')->user()->id;
        
        $cartItem = CartItem::where('user_id', $userId)->where('course_id', $courseId);

        if(!($cartItem->exists())){
            return customResponse(null, "Course is not in cart", 400, StatusCodesEnum::FAILED);
        }

        $cartItem->delete();

        return customResponse(null, "Course was removed from cart", 200, StatusCodesEnum::DONE);

    }
}
