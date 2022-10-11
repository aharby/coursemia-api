<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Courses\Models\WnatedToLearnCourse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CourseLectureResource extends JsonResource
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
        $want_to_learn = WnatedToLearnCourse::where(['course_id' => $request->course_id, 'user_id' => $user->id])
            ->first();
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'url'           => $this->url,
            'description'   => $this->description,
            'want_to_learn' => $want_to_learn ? true : false,
            'is_free_content'=> (boolean)$this->is_free_content,
        ];
    }
}
