<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResorce extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->device_name,
            'is_tablet'         => (boolean)$this->is_tablet
        ];
    }
}
