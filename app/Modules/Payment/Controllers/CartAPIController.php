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
    protected $user, $guestDevice;

    public function getUserOrGuest(){

        $this->user = auth('api')->user();

        $this->guestDevice = GuestDevice::where('guest_device_id', request()->header('device-id'))
                        ->first();

        if(isset($this->user) && isset($this->guestDevice))
            throw new \Exception(__('Server Error: device exists for user and guest'));

        return isset($this->user) ? $this->user : $this->guestDevice;
    }

    public function getCart() {
        
        $courses = $this->getUserOrGuest()->cartCourses->pluck('course');

        $totalPrice = $courses->sum('price');
        $courseCount = $courses->count();

        return customResponse([
            'total_price' => $totalPrice,
            'course_count' => $courseCount
        ], __('api.Fetched cart courses successfully'), 200, StatusCodesEnum::DONE);

    }
    public function getCourses()
    {
        $responseData = null;

        $courses = $this->getUserOrGuest()->cartCourses->pluck('course');
        
        if($courses)
            $responseData = CoursesResource::collection($courses);
    
        return customResponse($responseData, __('api.Fetched cart courses successfully'), 200, StatusCodesEnum::DONE);
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
            return customResponse(null, __('api.Course already in cart'), 400, StatusCodesEnum::FAILED);
        }

        $cartCourse = new CartCourse();

        $cartCourse->course_id = $courseId;

        if(isset($this->user))
            $cartCourse->user_id = $this->user->id;
        else
            $cartCourse->guest_device_id = $this->guestDevice->id;

        $cartCourse->save();

        return customResponse(null, __('api.Course added to cart'), 200, StatusCodesEnum::DONE);

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
            return customResponse(null, __('api.Course is not in cart'), 400, StatusCodesEnum::FAILED);

        if(isset($this->user))
            CartCourse::where(['user_id' => $this->user->id, 'course_id' => $courseId])->delete();
        else
            CartCourse::where(['guest_device_id' => $this->guestDevice->id, 'course_id' => $courseId])->delete();

        return customResponse(null, __('api.Course was removed from cart'), 200, StatusCodesEnum::DONE);

    }
}
