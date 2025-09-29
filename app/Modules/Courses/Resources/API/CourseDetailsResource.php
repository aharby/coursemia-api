<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Category\Resources\API\FlashCardCategoriesResource;
use App\Modules\Category\Resources\API\LectureCategoriesResource;
use App\Modules\Category\Resources\API\NoteCategoriesResource;
use App\Modules\Category\Resources\API\QuestionCategoriesResource;
use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseUser;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailsResource extends JsonResource
{
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

        $images = $this->images()->pluck('image');

        $price_after_discount = $this->price_after_discount;

        $expire = isset($this->expire_date)
            ? Carbon::parse($this->expire_date)->format('Y-m-d')
            : $this->expire_duration;

        // content
        $lectures = $this->lectures();
        $notes = $this->notes();
        $questions = $this->questions();
        $flashcards = $this->flashCards();
        
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
            'questions_categories' => QuestionCategoriesResource::collection($this->getContentCategories($questions)),
            'flash_cards_categories' => FlashCardCategoriesResource::collection($this->getContentCategories($flashcards)),
            'lectures_categories' => LectureCategoriesResource::collection($this->getContentCategories($lectures)),
            'notes_categories' => NoteCategoriesResource::collection($this->getContentCategories($notes))
        ];
    }

    public function getContentCategories($content)
    {
        $categoryIds = $content->pluck('category_id')->filter()->unique();

        $categories = Category::whereIn('id', $categoryIds)->get();

        $parentIds = $categories->pluck('parent_id')->filter()->unique();

        $parents = Category::whereIn('id', $parentIds)->get()->keyBy('id');

        // Replace children with parents if parent exists
        $categories = $categories->map(function ($category) use ($parents) {
            return $category->parent_id
                ? $parents->get($category->parent_id)
                : $category;
        })
        ->unique()
        ->sortBy('id');                  

        return $categories;
    }

}
