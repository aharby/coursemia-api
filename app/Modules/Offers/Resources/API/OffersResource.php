<?php

namespace App\Modules\Offers\Resources\API;

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
            'allowed_courses' => [
                // @toDo get data from courses table
                'id'    => 1,
                'title' => 'Course'
            ]
        ];
    }
}
