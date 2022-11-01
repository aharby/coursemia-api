<?php

namespace App\Modules\Events\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ListAdminEventsIndex extends JsonResource
{
    public function toArray($request)
    {
        return array(
            "id" => $this->id,
            "title_en" => $this->translate('en')->title,
            "title_ar" => $this->translate('ar')->title,
            "event_url" => $this->event_url,
            "image" => image($this->image, 'large'),
            "is_active" => (bool)$this->is_active,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        );
    }
}
