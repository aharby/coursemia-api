<?php

namespace App\Modules\WantToLearn\Resources;

use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Users\Resources\UserResorce;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WantToLearnResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'lecture'           => new CourseLectureResource($this->lecture),
        ];
    }
}
