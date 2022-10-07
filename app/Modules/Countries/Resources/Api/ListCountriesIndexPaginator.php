<?php

namespace App\Modules\Countries\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListCountriesIndexPaginator extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "data" => ListCountriesIndex::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
        ];
    }
}
