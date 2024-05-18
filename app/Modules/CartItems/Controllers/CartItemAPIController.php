<?php

namespace App\Modules\CartItems\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use App\Modules\CartItems\Models\CartItem;

use App\Modules\Courses\Resources\API\CoursesResource;

class CartItemAPIController extends Controller
{
    public function getCourses()
    {
        $user = auth('api')->user();

        $courses = CartItem::where('user_id', $user->id)
        ->with('course')
        ->get()
        ->pluck('course'); // Extract the item from the relationship
    
        return customResponse(CoursesResource::collection($courses), __("Fetched cart courses successfully"), 200, StatusCodesEnum::DONE);

    }

    public function addCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $cartItem = new CartItem();
        $cartItem->user_id = auth('api')->user()->id;
        $cartItem->course_id = $request['course_id'];
        $cartItem->save();

        return customResponse(null, "Course added to cart", 200, StatusCodesEnum::DONE);

    }

    public function removeCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        if ($validator->fails()){
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $userId = auth('api')->user()->id;
        $courseId = $request['course_id']; 
        
        CartItem::where('user_id', $userId)->where('course_id', $courseId)->delete();

        return customResponse(null, "Course was removed from cart", 200, StatusCodesEnum::DONE);

    }
}
