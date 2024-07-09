<?php

namespace App\Modules\WantToLearn\Flashcards\Resources;

use App\Modules\Courses\Resources\API\FlashCardsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WantToLearnFlashcardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'flashcard'           => new FlashCardsResource($this->flashcard),
        ];
    }
}
