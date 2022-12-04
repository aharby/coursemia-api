<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseLecture;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class LectureCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $parent = $this->parent;
        if (isset($parent)){
            $id = $parent->id;
            $title = $parent->title;
            $lecs = CourseLecture::whereIn('category_id' , $parent->subs()->pluck('id')->toArray())
                ->where('is_free_content', 1)->first();
            $subs = SubCategoriesResource::collection($parent->subs);
        }else{
            $id = $this->id;
            $title = $this->title;
            $lecs = CourseLecture::where('category_id' , $id)
                ->where('is_free_content', 1)->first();
            $subs = SubCategoriesResource::collection($this->subs);
        }
        return [
            'id'            => $id,
            'title'         => $title,
            'have_free_content' => $lecs ? true : false,
            'subs'          => $subs
        ];
    }
}
