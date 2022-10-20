<?php

namespace App\Modules\Offers\Resources\API;

use App\Modules\Courses\Resources\API\CourseDetailsResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class OffersResource extends JsonResource
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
            'title'         => $this->{'title_'.App::getLocale()},
            'image'         => asset($this->image),
            'expiration_date' => $this->expiration_date,
            'offer_value'   => (string)$this->offer_value,
            'offer_type'    => $this->offer_type,
            'extra_info'    => $this->offer_code,
            'allowed_courses' => CourseDetailsResource::collection($this->courses)
        ];
    }
}
