<?php

namespace App\Modules\WantToLearn\Questions\Resources;

use App\Modules\Courses\Resources\API\QuestionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WantToLearnQuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'question'           => new QuestionResource($this->question),
        ];
    }
}
