<?php

namespace App\Modules\WantToLearn\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WantToLearnCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "posts"             => WantToLearnResource::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
        ];
    }
}
