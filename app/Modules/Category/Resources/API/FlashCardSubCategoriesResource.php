<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\CourseFlashcard;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashCardSubCategoriesResource extends JsonResource
{
    public function toArray($request)
    {
        $lecs = CourseFlashcard::where('category_id' , $this->id)->where('is_free_content', 1)->first();
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'have_free_content' => $lecs ? true : false
        ];
    }
}
