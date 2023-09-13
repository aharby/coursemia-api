<?php

namespace App\Modules\Post\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PostOwnerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'profile_image'     => asset($this->photo),
            'full_name'         => $this->full_name,
        ];
    }
}
