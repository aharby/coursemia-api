<?php

namespace App\Modules\Post\Resources\Api;

use App\Modules\Users\Resources\UserResorce;
use App\UserFollow;
use Illuminate\Http\Resources\Json\JsonResource;

class PostUserResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth('api')->user();
        $following = UserFollow::where(['follower_id' => $user->id, 'followed_id' => $this->id])->first();
        return [
            "user"          => new UserResorce($this),
            "is_following"  => $following ? true : false,
            "posts"         => PostsResource::collection($this->posts()->orderByDesc('created_at')->get())
        ];
    }
}
