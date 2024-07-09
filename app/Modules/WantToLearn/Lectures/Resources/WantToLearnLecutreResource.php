<?php

namespace App\Modules\WantToLearn\Lectures\Resources;

use App\Modules\Courses\Resources\API\CourseLectureResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WantToLearnLectureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'lecture'           => new CourseLectureResource($this->lecture),
        ];
    }
}
