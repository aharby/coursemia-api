<?php

namespace App\Modules\Category\Resources\API;

use App\Modules\Courses\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionCategoriesResource extends JsonResource
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
            $lecs = Question::whereIn('category_id' , $parent->subs()->pluck('id')->toArray())
                ->where('is_free_content', 1)->first();
            $subs = QuestionSubCategoriesResource::collection($parent->subs);
        }else{
            $id = $this->id;
            $title = $this->title;
            $lecs = Question::where('category_id' , $id)->where('is_free_content', 1)->first();
            $subs = QuestionSubCategoriesResource::collection($this->subs);
        }
        return [
            'id'            => $id,
            'title'         => $title,
            'have_free_content' => $lecs ? true : false,
            'subs'          => $subs
        ];
    }
}
