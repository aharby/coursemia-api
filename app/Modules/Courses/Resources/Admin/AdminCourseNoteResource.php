<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
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
            $parent = $this->category->parent->title;
            $child = $this->category->title;
        }else{
            $parent = $this->category->title;
            $child = '';
        }
        return [
            'id'            => $this->id,
            'url'           => asset($this->url),
            'title_en'      => $this->translate('en')->title,
            'title_ar'      => $this->translate('ar')->title,
            'course_id'     => $this->course_id,
            'category_id'   => $this->category_id,
            'category'      => $parent,
            'sub_category'  => $child,
            'course'        => $this->course->title_en,
            'is_active'     => (bool)$this->is_active,
            'is_free_content'     => (bool)$this->is_free_content,
            'status'        => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
