<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\CourseLecture;
use Illuminate\Http\Resources\Json\JsonResource;

class LectureCategoriesResource extends JsonResource
{
    public function toArray($request)
    {
        $parent = $this->parent;
        if (isset($parent)){
            $id = $parent->id;
            $title = $parent->title;
            $lecs = CourseLecture::whereIn('category_id' , $parent->subs()->pluck('id')->toArray())
                ->where('is_free_content', 1)->first();
            $subs = LectureSubCategoriesResource::collection($parent->subs);
        }else{
            $id = $this->id;
            $title = $this->title;
            $lecs = CourseLecture::where('category_id' , $id)
                ->where('is_free_content', 1)->first();
            $subs = LectureSubCategoriesResource::collection($this->subs);
        }
        return [
            'id'            => $id,
            'title'         => $title,
            'have_free_content' => $lecs ? true : false,
            'subs'          => $subs
        ];
    }
}
