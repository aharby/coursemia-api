<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class LecturesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $extensions = ['png','jpg','jpeg'];
        $checkImage = substr($this->video_thumb,strpos($this->video_thumb, '.')+1);
        if (in_array($checkImage, $extensions)){
            $image = asset($this->video_thumb);
        }else{
            $image = asset('no-image.jpg');
        }
        if (isset($this->category->parent)){
            $parent = $this->category->parent->title;
            $child = $this->category->title;
        }else{
            $parent = $this->category->title;
            $child = '';
        }
        return [
            'id'            => $this->id,
            'title_en'      => $this->title_en,
            'title_ar'      => $this->title_ar,
            'url'           => $this->url,
            'course_id'     => $this->course_id,
            'category_id'   => $this->category_id,
            'image'         => $image,
            'description_en'=> $this->description_en,
            'description_ar'=> $this->description_ar,
            'category'      => $parent,
            'sub_category'  => $child,
            'course'        => $this->course->title_en,
            'is_active'     => (bool)$this->is_active,
            'is_free_content'     => (bool)$this->is_free_content,
            'status'        => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
