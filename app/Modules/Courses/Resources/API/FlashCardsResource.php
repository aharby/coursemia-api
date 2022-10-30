<?php

namespace App\Modules\Courses\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashCardsResource extends JsonResource
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
            'front'         => $this->front,
            'back'          => $this->back,
        ];
    }
}
