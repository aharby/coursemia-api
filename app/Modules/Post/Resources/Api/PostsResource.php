<?php

namespace App\Modules\Post\Resources\Api;

use App\Enums\PostTypeEnum;
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
        $type = PostTypeEnum::TEXT;
        if (isset($this->image)){
            $image = asset($this->image);
            $type = PostTypeEnum::TEXT_WITH_IMAGE;
        }
        if (isset($this->file)){
            $file = asset($this->file);
            $type == PostTypeEnum::TEXT_WITH_IMAGE ? $type = PostTypeEnum::TEXT_WITH_IMAGE_AND_FILE : $type = PostTypeEnum::TEXT_WITH_FILE;
        }
        if (isset($this->video)){
            $video = asset($this->video);
            $type == PostTypeEnum::TEXT_WITH_IMAGE_AND_FILE ? $type = PostTypeEnum::TEXT_WITH_IMAGE_AND_FILE_AND_VIDEO : $type = PostTypeEnum::TEXT_WITH_VIDEO ;
        }
        return [
            "id"        => $this->id,
            "type"      => $type,
            "is_liked"  => $this->is_liked,
            "is_loved"  => $this->is_loved,
            "content"   => $this->content,
            "image"     => $image,
            "file"      => $file,
            "video"     => $video,
            "hashtags"  => PostHashtagsResource::collection($this->hashtags),
            "likes"     => $this->likes()->where('type', 'like')->count(),
            "loves"     => $this->likes()->where('type', 'love')->count(),
            "user"      => new PostOwnerResource($this->user),
            "comments"  => PostCommentsResource::collection($this->whenLoaded('comments')),
            "comments_count"  => $this->comments_count,
            "date"      =>  Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
