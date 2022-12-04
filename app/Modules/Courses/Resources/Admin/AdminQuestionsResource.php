<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
use App\Modules\Courses\Resources\Admin\AdminAnswersResource;
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
        if (isset($this->category->parent)){
            $parent = $this->category->parent->title;
            $child = $this->category->title;
        }else{
            $parent = $this->category->title;
            $child = '';
        }
        $extensions = ['png','jpg','jpeg'];
        $checkImage = substr($this->image,strpos($this->image, '.')+1);
        $checkExplanationImage = substr($this->explanation_image,strpos($this->explanation_image, '.')+1);
        if (in_array($checkImage, $extensions)){
            $image = asset($this->image);
        }else{
            $image = asset('no-image.jpg');
        }
        if (in_array($checkExplanationImage, $extensions)){
            $explanationImage = asset($this->explanation_image);
        }else{
            $explanationImage = asset('no-image.jpg');
        }
        return [
            'id'            => $this->id,
            'image'         => $image,
            'url'           => asset($this->url),
            'title_en'      => $this->translate('en')->title,
            'title_ar'      => $this->translate('ar')->title,
            'description_en'=> $this->translate('en')->description,
            'description_ar'=> $this->translate('ar')->description,
            'explanation_en'=> $this->translate('en')->explanation,
            'explanation_ar'=> $this->translate('ar')->explanation,
            'explanation_image'=> $explanationImage,
            'answers'       => AdminAnswersResource::collection($this->answers),
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
