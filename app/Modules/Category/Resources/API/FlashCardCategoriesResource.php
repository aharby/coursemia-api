<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\CourseFlashcard;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashCardCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $id = $this->id;
        $title = $this->title;
        $subs = FlashcardSubCategoriesResource::collection($this->subs);
        $haveFreeContent = CourseFlashcard::where('category_id' , $id)
                            ->where('is_free_content', 1)
                            ->first()
                        || collect($subs->resolve())
                            ->pluck('have_free_content')->contains(true);
        
        return [
            'id'            => $id,
            'title'         => $title,
            'have_free_content' => $haveFreeContent,
            'subs'          => $subs
        ];
    }
}
