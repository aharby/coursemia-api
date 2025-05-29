<?php

namespace App\Modules\Courses\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Modules\WantToLearn\Flashcards\Models\WantToLearnFlashcard;

class FlashCardsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth('api')->user();
        $want_to_learn = false;

        if (isset($user)){
            $want_to_learn = WantToLearnFlashcard::where(['flashcard_id' => $this->id, 'user_id' => $user->id])
                ->first();
        }

        return [
            'id'            => $this->id,
            'front'         => $this->front,
            'back'          => $this->back,
            'want_to_learn' => $want_to_learn ? true : false,
            'is_free_content'=> (boolean)$this->is_free_content
        ];
    }
}
