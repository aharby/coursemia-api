<?php

namespace App\Modules\Specialities\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListAdminSpecialitiesIndexPaginator extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "data" => ListAdminSpecialitiesIndex::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
        ];
    }
}
