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
        $image = null;
        $explanation = null;
        $explanation_image = null;
        $explanation_voice = null;
        if (isset($this->image)){
            $image = asset($this->image);
        }
        if (isset($this->translated_explanation)){
            $explanation = $this->translated_explanation;
        }
        if (isset($this->explanation_voice)){
            $explanation_voice = asset($this->explanation_voice);
        }
        if (isset($this->explanation_image)){
            $explanation_image = asset($this->explanation_image);
        }
        if (!isset($explanation) && !isset($this->explanation_voice) && !isset($this->explanation_image)){
            $explanationObject = null;
        }else{
            $explanationObject = [
                'text'  => $explanation,
                'image' => $explanation_image,
                'voice' => $explanation_voice
            ]
        }
        return [
            'id'            => $this->id,
            'question'      => $this->translated_title,
            'image'         => $image,
            'is_free_content' => (boolean)$this->is_free_content,
            'answers'       => QuestionAnswersResource::collection($this->answers),
            'explanation'   => $explanationObject
        ];
    }
}
