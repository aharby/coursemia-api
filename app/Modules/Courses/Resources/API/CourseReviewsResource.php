<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Courses\Models\CourseUser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CourseReviewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $request->user();
        $can_add_rate = false;
        $course_user_check = CourseUser::where(['user_id' => $user->id, 'course_id' => $this->id])->first();
        if (isset($course_user_check)){
            $reviews_check = $this->reviews()->where('user_id', $user->id)->first();
            if (!isset($reviews_check)){
                $can_add_rate = true;
            }
        }
        return [
            'rate'                  => $this->rate,
            'reviews_count'         => $this->reviews()->count(),
            'number_of_5_stars'     => $this->reviews()->where('rate', '=',5)->count(),
            'number_of_4_stars'     => $this->reviews()->where('rate', '=',4)->count(),
            'number_of_3_stars'     => $this->reviews()->where('rate', '=',3)->count(),
            'number_of_2_stars'     => $this->reviews()->where('rate', '=',2)->count(),
            'number_of_1_stars'     => $this->reviews()->where('rate', '=',1)->count(),
            'can_add_review'        => $can_add_rate,
            'reviews'               => UserCourseReviewResource::collection($this->reviews)
        ];
    }
}
