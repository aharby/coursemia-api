<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
use App\Modules\Courses\Resources\Admin\ValueTextCategoriesResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AdminCourseNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (isset($this->category->parent)){
            $parent = $this->category->parent->title_en;
            $subs = $this->category->parent->subs()->get();
            $subs = ValueTextCategoriesResource::collection($subs);
            $child = $this->category->title_en;
            $cat_id = $this->category->parent_id;
            $sub_cat_id = $this->category_id;
        }else{
            $parent = $this->category->title_en;
            $subs = $this->category->subs()->get();
            $subs = ValueTextCategoriesResource::collection($subs);
            $child = $this->category->title_en;
            $cat_id = $this->category_id;
            $sub_cat_id = $this->category_id;
        }
        return [
            "category_id" => $cat_id,
            "sub_category_id" => $sub_cat_id,
            "subs"          => $subs,
            "sub_category"    => $child,
            'id'            => $this->id,
            'url'           => asset($this->url),
            'title_en'      => $this->translate('en')->title,
            'title_ar'      => $this->translate('ar') ? $this->translate('ar')->title : '',
            'course_id'     => $this->course_id,
            'category'      => $parent,
            'course'        => $this->course->title_en,
            'is_active'     => (bool)$this->is_active,
            'is_free_content'     => (bool)$this->is_free_content,
            'status'        => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d h:i a'),
            "created_by" => $this->admin ? $this->admin->name : ''
        ];
    }
}
