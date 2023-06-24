<?php

namespace App\Modules\FAQs\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FAQCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "data" => FAQResource::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
        ];
    }
}
