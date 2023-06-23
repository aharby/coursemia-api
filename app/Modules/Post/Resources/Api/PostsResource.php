<?php

namespace App\Modules\Post\Resources\Api;

use App\Modules\Users\Resources\UserResorce;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
{
    public function toArray($request)
    {
        $image = null;
        $file = null;
        $video = null;
        if (isset($this->image)){
            $image = asset($this->image);
        }
        if (isset($this->file)){
            $file = asset($this->file);
        }
        if (isset($this->video)){
            $video = asset($this->video);
        }
        return [
            "id"        => $this->id,
            "is_liked"  => $this->is_liked,
            "is_loved"  => $this->is_loved,
            "content"   => $this->content,
            "image"     => $image,
            "file"      => $file,
            "video"     => $video,
            "hashtags"  => PostHashtagsResource::collection($this->hashtags),
            "likes"     => $this->likes()->where('type', 'like')->count(),
            "loves"     => $this->likes()->where('type', 'love')->count(),
//            "user"      => new UserResorce($this->whenLoaded($this->user)),
            "comments"  => PostCommentsResource::collection($this->whenLoaded('comments')),
            "date"      =>  Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
