<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AdminQuestionsResource extends JsonResource
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
            'image'         => asset($this->image),
            'url'           => asset($this->url),
            'title_en'      => $this->translate('en')->title,
            'title_ar'      => $this->translate('ar')->title,
            'description_en'=> $this->translate('en')->description,
            'description_ar'=> $this->translate('ar')->description,
            'explanation_en'=> $this->translate('en')->explanation,
            'explanation_ar'=> $this->translate('ar')->explanation,
            'explanation_image'=> asset($this->explanation_image),
            'course_id'     => $this->course_id,
            'category_id'   => $this->category_id,
            'category'      => $this->category->title_en,
            'course'        => $this->course->title_en,
            'is_active'     => (bool)$this->is_active,
            'is_free_content'     => (bool)$this->is_free_content,
            'status'        => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
