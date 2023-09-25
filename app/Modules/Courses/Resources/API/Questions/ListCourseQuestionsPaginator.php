<?php

namespace App\Modules\Courses\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListCourseQuestionsPaginator extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            "data" => ListCourseQuestions::collection($this->collection),
            'current_page'      => $this->currentPage(),
            'last_page'         => $this->lastPage(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'total'             => $this->total(),
            'is_last_page'      => $this->currentPage() == $this->lastPage() ? true : false
        ];
    }
}
