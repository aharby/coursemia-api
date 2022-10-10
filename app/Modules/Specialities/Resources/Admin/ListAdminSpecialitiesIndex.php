<?php

namespace App\Modules\Specialities\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ListAdminSpecialitiesIndex extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title_en" =>$this->translate('en')->title,
            "title_ar" =>$this->translate('ar')->title,
            "image" => image($this->image , 'large'),
            "is_active" => (bool) $this->is_active ,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
