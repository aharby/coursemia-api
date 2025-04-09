<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use App\Modules\Payment\Models\CartCourse;

use App\Modules\Courses\Resources\API\CoursesResource;

use App\Models\GuestDevice;
use function PHPUnit\Framework\throwException;

class CartAPIController extends Controller
{
    protected $user, $guest_device;

    public function getUserOrGuest(){

        $this->user = auth('api')->user();

        $this->guest_device = GuestDevice::where('guest_device_id', request()->header('device-id'))
                        ->first();

        if(isset($this->user) && isset($this->guest_device))
            throw new \Exception(__('Server Error: device exists for user and guest'));

        return isset($this->user) ? $this->user : $this->guest_device;
    }

    public function getCourses()
    {
        $response_data = null;

        $courses = $this->getUserOrGuest()->cartCourses;
        
        if($courses)
            $response_data = CoursesResource::collection($courses);
    
        return customResponse($response_data, __("Fetched cart courses successfully"), 200, StatusCodesEnum::DONE);
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

        $courseAlreadyInCart = $this->getUserOrGuest()->cartCourses->contains('course_id', $courseId);

        if($courseAlreadyInCart){
            return customResponse(null, "Course already in cart", 400, StatusCodesEnum::FAILED);
        }

        $cartCourse = new CartCourse();

        $cartCourse->course_id = $courseId;

        if(isset($this->user))
            $cartCourse->user_id = $this->user->id;
        else
            $cartCourse->guest_device_id = $this->guest_device->id;

        $cartCourse->save();

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
        
        $courseAlreadyInCart = $this->getUserOrGuest()->cartCourses->contains('course_id', $courseId);

        if(!$courseAlreadyInCart)
            return customResponse(null, "Course is not in cart", 400, StatusCodesEnum::FAILED);

        if(isset($this->user))
            CartCourse::where(['user_id' => $this->user->id, 'course_id' => $courseId])->delete();
        else
            CartCourse::where(['user_id' => $this->guest_device->id, 'course_id' => $courseId])->delete();

        return customResponse(null, "Course was removed from cart", 200, StatusCodesEnum::DONE);

    }
}
