<?php

namespace App\Modules\Courses\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ListQuestionAnswer extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "answer" => $this->translated_answer,
            "is_correct" => $this->is_correct,
            "answer_choosed_percentage" => $this->chosen_percentage,
        ];
    }
}
