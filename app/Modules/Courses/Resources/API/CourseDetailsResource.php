<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\Category\Resources\API\CategoriesResource;
use App\Modules\Category\Resources\API\FlashCardsCategoriesResource;
use App\Modules\Category\Resources\API\LectureCategoriesResource;
use App\Modules\Category\Resources\API\NotesCategoriesResource;
use App\Modules\Category\Resources\API\QuestionsCategoriesResource;
use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseUser;
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
        $user = auth('api')->user();
        if (isset($user)){
            $user_id = auth('api')->user()->id;
            $course_user = CourseUser::where(['course_id' => $this->id, 'user_id' => $user_id])->first();
            if (isset($course_user))
                $is_purchased = true;
        }
        $lectures = $this->lectures();
        $notes = $this->notes();
        $questions = $this->questions();
        $flashcards = $this->flashCards();
        $lectures_categories = $lectures->pluck('category_id');
        $notes_categories = $notes->pluck('category_id');
        $questions_categories = $questions->pluck('category_id');
        $flashcards_categories = $flashcards->pluck('category_id');
        $images = $this->images()->pluck('image');
        $offer_courses_check = OfferCourse::where('course_id', $this->id)->first();
        $price_after_discount = null;
//        if (isset($offer_courses_check)){
//            $value = $offer_courses_check->offer->offer_value;
//            $price_after_discount = $this->price - (($value*$this->price) / 100);
//        }
        $price_after_discount = $this->price_after_discount;
        if (isset($this->expire_date)){
            $expire = Carbon::parse($this->expire_date)->format('Y-m-d');
        }else{
            $expire = $this->expire_duration;
        }
        return [
            'id'            => $this->id,
            'is_in_my_cart' => false, //@todo implement is in my cart
            'is_purchased'  => $is_purchased,
            'title'         => $this->title,
            'cover_image'   => asset($this->cover_image),
            'images'        => $images,
            'price'         => (double)$this->price,
            'price_after_discount'         => (double)$price_after_discount,
            'rate'          => (float)$this->rate,
            'description'   => $this->description,
            'reviews_count' => $this->reviews()->count(),
            'expiration_date' => $expire,
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
