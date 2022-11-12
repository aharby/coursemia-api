<?php

namespace App\Modules\Countries\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ListAdminConfigsIndex extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "field_type" => $this->field_type,
            "type" => $this->type,
            "field" => $this->field,
            "label_en" => $this->translate('en')->label,
            "label_ar" => $this->translate('ar')->label,
            "value_en" => $this->translate('en')->value,
            "value_ar" => $this->translate('ar')->value,
        ];
    }
}
