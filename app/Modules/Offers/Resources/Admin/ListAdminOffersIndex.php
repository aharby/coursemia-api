<?php

namespace App\Modules\Offers\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Offers\Enums\OffersEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ListAdminOffersIndex extends JsonResource
{
    public function toArray($request)
    {
        return array(
            "id" => $this->id,
            "title_en" => $this->translate('en')->title,
            "title_ar" => $this->translate('ar')->title,
            "image" => image($this->image, 'large'),
            "is_active" => (bool)$this->is_active,
            "selected_courses"   => OfferCoursesAdminResource::collection($this->courses),
            "expiration_date" => (string)$this->expiration_date,
            "offer_value" => (string)$this->offer_value,
            "offer_type" => OffersEnum::getOfferType($this->offer_type),
            'offer_code' => (string)$this->offer_code,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        );
    }
}
