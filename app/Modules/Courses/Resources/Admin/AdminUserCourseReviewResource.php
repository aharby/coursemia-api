<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AdminUserCourseReviewResource extends JsonResource
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
            'id'        => $this->id,
            'rate'      => $this->rate,
            'owner_name'=> $this->user->full_name,
            'owner_image'=> asset($this->user->photo),
            'comment'   => $this->comment,
            'created_at'=> Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
