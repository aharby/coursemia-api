<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CoursesResource extends JsonResource
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
            'title_en'      => $this->title_en,
            'title_ar'      => $this->title_ar,
            'image'         => asset($this->cover_image),
            'description_en'=> $this->description_en,
            'description_ar'=> $this->description_ar,
            'speciality'    => $this->speciality->title,
            'speciality_id' => $this->speciality_id,
            'rate'          => $this->rate,
            'price'         => $this->price,
            'is_active'     => (bool)$this->is_active,
            'status'        => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
            'expire_date'   => Carbon::parse($this->expire_date)->format('Y-m-d'),
            'lectures_count'=> $this->lectures()->count(),
            'notes_count'   => $this->notes()->count(),
            'questions_count'=> $this->questions()->count(),
            'flash_cards_count'=> $this->flashCards()->count()
        ];
    }
}
