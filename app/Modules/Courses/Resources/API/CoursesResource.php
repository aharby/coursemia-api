<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Courses\Models\CourseUser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CoursesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_purchased = false;
        $user_id = $request->user()->id;
        $course_user = CourseUser::where(['course_id' => $this->id, 'user_id' => $user_id])->first();
        if (isset($course_user))
            $is_purchased = true;
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'cover_image'   => asset($this->cover_image),
            'description'   => $this->description,
            'is_purchased'  => (boolean)$is_purchased,
            'lectures_count'=> $this->lectures()->count(),
            'notes_count'   => $this->notes()->count(),
            'questions_count'=> $this->questions()->count(),
            'flash_cards_count'=> $this->flashCards()->count()
        ];
    }
}
