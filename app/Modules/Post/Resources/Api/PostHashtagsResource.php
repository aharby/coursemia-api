<?php

namespace App\Modules\Post\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostHashtagsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            'hashtag'           => $this->hashtag
        ];
    }
}
