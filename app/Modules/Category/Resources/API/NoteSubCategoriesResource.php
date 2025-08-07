<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\CourseNote;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class NoteSubCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lecs = CourseNote::where('category_id' , $this->id)->where('is_free_content', 1)->first();
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'have_free_content' => $lecs ? true : false
        ];
    }
}
