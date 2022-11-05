<?php

namespace App\Modules\Offers\Resources\API;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Resources\API\CoursesResource;
use App\Modules\Offers\Enums\OffersEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class OffersResource extends JsonResource
{
    public function toArray($request)
    {
        $image = null;
        if (isset($this->image)){
            $image = image($this->image, 'large');
        }
        return [
            'id'            => $this->id,
            'title'         => $this->translated_title,
            'image'         => $image,
            'expiration_date' => $this->expiration_date,
            'offer_value'   => (string)$this->offer_value,
            'offer_type'    => $this->offer_type,
            'extra_info'    => $this->offer_code,
            'allowed_courses' => CoursesResource::collection($this->courses)
        ];
    }
}
