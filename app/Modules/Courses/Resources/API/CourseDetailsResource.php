<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Category\Resources\API\CategoriesResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CourseDetailsResource extends JsonResource
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
            'id'            => $this->id,
            'title'         => $this->title,
            'cover_image'   => asset($this->cover_image),
            'images'        => [],
            'price'         => (double)$this->price,
            'rate'          => (float)$this->rate,
            'description'   => $this->description,
            'reviews_count' => $this->reviews()->count(),
            'expiration_date' => $this->expire_date,
            'lectures_count'=> $this->lectures()->count(),
            'notes_count'   => $this->notes()->count(),
            'questions_count'=> $this->questions()->count(),
            'flash_cards_count'=> $this->flashCards()->count(),
            'lectures_categories' => $this->courseLectureCategories
        ];
    }
}
