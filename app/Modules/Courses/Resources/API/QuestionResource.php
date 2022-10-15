<?php

namespace App\Modules\Courses\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'question'      => $this->translated_title,
            'image'         => asset($this->image),
            'answers'       => QuestionAnswersResource::collection($this->answers),
            'explanation'   => [
                'text'  => $this->translated_explanation,
                'image' => asset($this->explanation_image),
                'voice' => asset($this->explanation_voice)
            ]
        ];
    }
}
