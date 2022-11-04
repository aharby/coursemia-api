<?php

namespace App\Modules\Offers\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Offers\Enums\OffersEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferCoursesAdminResource extends JsonResource
{
    public function toArray($request)
    {
        return array(
            "value" => ''.$this->id.'',
            "title" => $this->title_en,
        );
    }
}
