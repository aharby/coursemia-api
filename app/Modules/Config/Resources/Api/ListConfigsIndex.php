<?php

namespace App\Modules\Countries\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ListConfigsIndex extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "field_type" => $this->field_type,
            "type" => $this->type,
            "field" => $this->field,
            "label" => $this->translate(App::getLocale())->label,
            "value" => $this->translate(App::getLocale())->value,
        ];
    }
}
