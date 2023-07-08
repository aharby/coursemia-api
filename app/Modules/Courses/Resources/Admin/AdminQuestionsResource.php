<?php

namespace App\Modules\Courses\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
use App\Modules\Courses\Resources\Admin\AdminAnswersResource;
use App\Modules\Courses\Resources\Admin\ValueTextCategoriesResource;
use Carbon\Carbon;
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
        $explanation_voice = null;
        if (isset($this->explanation_voice))
            $explanation_voice = asset($this->explanation_voice);
        return [
            "category_id" => $cat_id,
            'admin_id'  => $this->admin_id,
            "sub_category_id" => $sub_cat_id,
            "subs"          => $subs,
            "sub_category"    => $child,
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
            'explanation_voice'=> $explanation_voice,
            'answers'       => AdminAnswersResource::collection($this->answers),
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
