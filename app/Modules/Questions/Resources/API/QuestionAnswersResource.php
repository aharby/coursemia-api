<?php

namespace App\Modules\Questions\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class QuestionAnswersResource extends JsonResource
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
            'answer'        => $this->answer,
            'is_correct'    => (boolean)$this->is_correct,
            'answer_choosed_percentage' => (double)$this->choosen_percentage
        ];
    }
}
