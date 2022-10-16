<?php

namespace App\Modules\Courses\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ListCourseQuestions extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->translated_title,
            "description" => $this->translated_description,
            "image" => image($this->image, 'large'),
            "explanation_text" => $this->translated_explanation,
            "explanation_image" => image($this->explanation_image, 'large'),
            "explanation_voice" => image($this->explanation_voice, 'large'),
            "answers" => ListQuestionAnswer::collection($this->answers),

        ];
    }
}
