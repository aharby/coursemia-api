<?php

namespace App\Modules\WantToLearn\Courses\Resources;

use App\Modules\Courses\Resources\API\CoursesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WantToLearnCourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'course'           => new CoursesResource($this->course),
        ];
    }
}
