<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\CourseLecture;
use Illuminate\Http\Resources\Json\JsonResource;

class LectureCategoriesResource extends JsonResource
{
    public function toArray($request)
    {
        $id = $this->id;
        $title = $this->title;
        $haveFreeContent = CourseLecture::where('category_id' , $id)
            ->where('is_free_content', 1)->first();
        $subs = LectureSubCategoriesResource::collection($this->subs);
    
        return [
            'id'            => $id,
            'title'         => $title,
            'have_free_content' => $haveFreeContent ? true : false,
            'subs'          => $subs
        ];
    }
}
