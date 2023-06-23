<?php

namespace App\Modules\Post\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentOwnerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'profile_image'     => asset($this->photo),
            'full_name'         => $this->full_name,
        ];
    }
}
