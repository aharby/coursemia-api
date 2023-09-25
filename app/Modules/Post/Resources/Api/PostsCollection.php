<?php

namespace App\Modules\Post\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "posts"             => PostsResource::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
            'is_last_page'      => $this->currentPage() == $this->lastPage() ? true : false
        ];
    }
}
