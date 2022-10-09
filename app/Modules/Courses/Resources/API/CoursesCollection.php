<?php

namespace App\Modules\Courses\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CoursesCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'current_page'      => $this->currentPage(),
            'is_last_page'      => $this->currentPage() == $this->lastPage() ? true : false,
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
            'courses'           => CoursesResource::collection($this),
        ];
    }
}
