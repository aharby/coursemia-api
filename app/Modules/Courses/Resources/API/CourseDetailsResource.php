<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Category\Resources\API\CategoriesResource;
use App\Modules\Category\Resources\API\FlashCardsCategoriesResource;
use App\Modules\Category\Resources\API\LectureCategoriesResource;
use App\Modules\Category\Resources\API\NotesCategoriesResource;
use App\Modules\Category\Resources\API\QuestionsCategoriesResource;
use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Payment\Models\CartCourse;
use App\Modules\Courses\Models\OfferCourse;
use Carbon\Carbon;
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
        $is_purchased = false;
        $is_in_cart = false;

        $user = auth('api')->user();
        if (isset($user)) {
            $user_id = $user->id;
            $course_user = CourseUser::where(['course_id' => $this->id, 'user_id' => $user_id])->first();
            if (isset($course_user)) {
                $is_purchased = true;
            }

            $is_in_cart = $user->cartCourses()->where(['course_id' => $this->id])->exists();
        }

        $lectures = $this->lectures();
        $notes = $this->notes();
        $questions = $this->questions();
        $flashcards = $this->flashCards();

        $lectures_categories_ids = $lectures->pluck('category_id')->unique()->filter();
        $notes_categories_ids = $notes->pluck('category_id')->unique()->filter();
        $questions_categories_ids = $questions->pluck('category_id')->unique()->filter();
        $flashcards_categories_ids = $flashcards->pluck('category_id')->unique()->filter();

        $images = $this->images()->pluck('image');

        $price_after_discount = $this->price_after_discount;

        $expire = isset($this->expire_date)
            ? Carbon::parse($this->expire_date)->format('Y-m-d')
            : $this->expire_duration;

        // Preserve category order
        $lecture_cats = Category::whereIn('id', $lectures_categories_ids)->get()->keyBy('id');
        $ordered_lecture_categories = $lectures_categories_ids->map(fn($id) => $lecture_cats[$id])->filter();

        $notes_cats = Category::whereIn('id', $notes_categories_ids)->get()->keyBy('id');
        $ordered_notes_categories = $notes_categories_ids->map(fn($id) => $notes_cats[$id])->filter();

        $questions_cats = Category::whereIn('id', $questions_categories_ids)->get()->keyBy('id');
        $ordered_questions_categories = $questions_categories_ids->map(fn($id) => $questions_cats[$id])->filter();

        $flashcards_cats = Category::whereIn('id', $flashcards_categories_ids)->get()->keyBy('id');
        $ordered_flashcards_categories = $flashcards_categories_ids->map(fn($id) => $flashcards_cats[$id])->filter();

        return [
            'id' => $this->id,
            'is_in_my_cart' => false, // @todo implement is in my cart
            'is_purchased' => $is_purchased,
            'is_in_cart' => $is_in_cart,
            'title' => $this->title,
            'cover_image' => asset($this->cover_image),
            'images' => $images,
            'price' => (double) $this->price,
            'price_after_discount' => (double) $price_after_discount,
            'rate' => (float) $this->rate,
            'description' => $this->description,
            'reviews_count' => $this->reviews()->count(),
            'expiration_date' => $expire,
            'lectures_count' => $lectures->count(),
            'notes_count' => $notes->count(),
            'questions_count' => $questions->count(),
            'flash_cards_count' => $flashcards->count(),

            // Ordered categories preserved
            'lectures_categories' => LectureCategoriesResource::collection($ordered_lecture_categories),
            'notes_categories' => NotesCategoriesResource::collection($ordered_notes_categories),
            'questions_categories' => QuestionsCategoriesResource::collection($ordered_questions_categories),
            'flash_cards_categories' => FlashCardsCategoriesResource::collection($ordered_flashcards_categories),
        ];
    }

}
