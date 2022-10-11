<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Courses\Models\WnatedToLearnCourse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CourseNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'url'           => $this->url,
            'is_free_content'=> (boolean)$this->is_free_content,
        ];
    }
}
