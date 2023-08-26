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
        $image = $this->image;
        $explanation = $this->translated_explanation;
        $explanation_image = $this->explanation_image;
        $explanation_voice = $this->explanation_voice;
        if (isset($image)){
            $image = asset($this->image);
        }
        if (isset($explanation_voice)){
            $explanation_voice = asset($this->explanation_voice);
        }
        if (isset($explanation_image)){
            $explanation_image = asset($this->explanation_image);
        }
        if ((!isset($explanation) || $explanation == "") && (!isset($this->explanation_voice) || $this->explanation_voice == "") && (!isset($this->explanation_image) || $this->explanation_image == "")){
            $explanationObject = null;
        }else{
            $explanationObject = [
                'text'  => $explanation,
                'image' => $explanation_image,
                'voice' => $explanation_voice
            ];
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
