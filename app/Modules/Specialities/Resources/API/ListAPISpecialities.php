<?php

namespace App\Modules\Specialities\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ListAPISpecialities extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" =>$this->translated_title,
            "image" => image($this->image , 'large'),
            "is_active" => (bool) $this->is_active ,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
