<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\WantToLearn\Lectures\Models\WantToLearnLecture;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $user = auth('api')->user();
        $want_to_learn = false;
        if (isset($user)){
            $want_to_learn = WantToLearnLecture::where(['lecture_id' => $this->id, 'user_id' => $user->id])
                ->first();

            $progress = $this->progress($user->id);

            $last_position = $progress ?  $progress->last_position : 0;
        }
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'url'           => $this->url,
            'thumbnail'     => $this->video_thumb ? asset($this->video_thumb) : null,
            'description'   => $this->description,
            'want_to_learn' => $want_to_learn ? true : false,
            'is_free_content'=> (boolean)$this->is_free_content,
            'course'        => $this->course->title,
            'last_position' => $last_position
        ];
    }
}
