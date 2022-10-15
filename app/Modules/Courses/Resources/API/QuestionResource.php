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
            'question'      => $this->question,
            'image'         => asset($this->image),
            'answers'       => QuestionAnswersResource::collection($this->answers),
            'explanation'   => [
                'text'  => $this->explanation,
                'image' => asset($this->explanation_image),
                'voice' => asset($this->explanation_voice)
            ]
        ];
    }
}
