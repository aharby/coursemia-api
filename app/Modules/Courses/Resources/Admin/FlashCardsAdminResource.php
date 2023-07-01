<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class FlashCardsAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (isset($this->category->parent)){
            $parent = $this->category->parent->title;
            $subs = $this->category->parent->subs()->get();
            $subs = ValueTextCategoriesResource::collection($subs);
            $child = $this->category->title;
            $cat_id = $this->category->parent_id;
            $sub_cat_id = $this->category_id;
        }else{
            $parent = $this->category->title;
            $subs = $this->category->subs()->get();
            $subs = ValueTextCategoriesResource::collection($subs);
            $child = $this->category->title;
            $cat_id = $this->category_id;
            $sub_cat_id = $this->category_id;
        }
        return [
            "id" => $this->id,
            "front_en" => $this->translate('en')->front,
            "front_ar" => $this->translate('ar')->front,
            "back_en" => $this->translate('en')->back,
            "back_ar" => $this->translate('ar')->back,
            "course_id" => $this->course_id,
            "course" => $this->course->title,
            "category_id" => $cat_id,
            "sub_category_id" => $sub_cat_id,
            "category" => $parent,
            "subs"      => $subs,
            "sub_category"    => $child,
            "is_active" => (bool)$this->is_active,
            "is_free_content" => $this->is_free_content,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d h:i a'),
            "created_by" => $this->admin ? $this->admin->name : ''
        ];
    }
}
