<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Category\Resources\API\CategoriesResource;
use App\Modules\Category\Resources\API\FlashCardsCategoriesResource;
use App\Modules\Category\Resources\API\LectureCategoriesResource;
use App\Modules\Category\Resources\API\NotesCategoriesResource;
use App\Modules\Category\Resources\API\QuestionsCategoriesResource;
use App\Modules\Courses\Models\Category;
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
        $lectures = $this->lectures();
        $notes = $this->notes();
        $questions = $this->questions();
        $flashcards = $this->flashCards();
        $lectures_categories = $lectures->pluck('category_id');
        $notes_categories = $notes->pluck('category_id');
        $questions_categories = $questions->pluck('category_id');
        $flashcards_categories = $flashcards->pluck('category_id');
        $images = $this->images()->pluck('image');
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'cover_image'   => asset($this->cover_image),
            'images'        => $images,
            'price'         => (double)$this->price,
            'rate'          => (float)$this->rate,
            'description'   => $this->description,
            'reviews_count' => $this->reviews()->count(),
            'expiration_date' => $this->expire_date,
            'lectures_count'=> $lectures->count(),
            'notes_count'   => $notes->count(),
            'questions_count'=> $questions->count(),
            'flash_cards_count'=> $flashcards->count(),
            'lectures_categories' => LectureCategoriesResource::collection(Category::whereIn('id', $lectures_categories)->get()),
            'notes_categories' => NotesCategoriesResource::collection(Category::whereIn('id', $notes_categories)->get()),
            'questions_categories' => QuestionsCategoriesResource::collection(Category::whereIn('id', $questions_categories)->get()),
            'flash_cards_categories' => FlashCardsCategoriesResource::collection(Category::whereIn('id', $flashcards_categories)->get()),
        ];
    }
}
