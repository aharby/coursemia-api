<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ImagesResource extends JsonResource
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
            'url'           => asset($this->image),
        ];
    }
}
