<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class UserCourseReviewResource extends JsonResource
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
            'rate'      => $this->rate,
            'owner_name'=> $this->user->full_name,
            'comment'   => $this->comment,
            'created_at'=> Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
