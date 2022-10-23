<?php

namespace App\Modules\Users\Admin\Resources;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseLecture;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class UserDeviceResource extends JsonResource
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
            'id'                        => $this->id,
            'device_id'                 => $this->device_id,
            'allow_push_notifications'  => $this->allow_push_notifications,
            'device_name'               => $this->device_name,
            'is_tablet'                 => $this->is_tablet,
            'device_type'               => $this->type,
            'created_at'                => Carbon::parse($this->created_at)->format('M d,Y')
        ];
    }
}
