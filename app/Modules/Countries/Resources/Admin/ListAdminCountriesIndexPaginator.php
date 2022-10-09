<?php

namespace App\Modules\Countries\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListAdminCountriesIndexPaginator extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "data" => ListAdminCountriesIndex::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
        ];
    }
}
