<?php

namespace App\Modules\Post\Resources\Api;

use App\Modules\Users\Resources\UserResorce;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowRequestsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "requested_at"      => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'user'              => new CommentOwnerResource($this->follower),
        ];
    }
}
