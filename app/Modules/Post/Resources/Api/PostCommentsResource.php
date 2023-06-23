<?php

namespace App\Modules\Post\Resources\Api;

use App\Modules\Users\Resources\UserResorce;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            'comment'           => $this->comment,
            'user'              => new CommentOwnerResource($this->user),
            'date'              => Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
