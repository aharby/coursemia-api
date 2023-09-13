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
        $type = 'text';
        if (isset($this->image)){
            $image = asset($this->image);
            $type = 'text_with_image';
        }
        if (isset($this->file)){
            $file = asset($this->file);
            $type == 'text_with_image' ? $type = 'text_with_image_and_file' : $type = 'text_with_file';
        }
        if (isset($this->video)){
            $video = asset($this->video);
            $type == 'text_with_image_and_file' ? $type = 'text_with_image_and_file_and_video' : $type = 'text_with_video' ;
        }
        $comments = $this->comments;
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
            "comments_count"  => sizeof($comments),
            "date"      =>  Carbon::parse($this->created_at)->format('Y-m-d h:i:s')
        ];
    }
}
