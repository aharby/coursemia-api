<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\WnatedToLearnCourse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AdminAnswersResource extends JsonResource
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
            'answer_en'     => $this->translate('en')->answer,
            'answer_ar'     => $this->translate('ar')->answer,
            'is_correct'    => $this->is_correct,
        ];
    }
}
