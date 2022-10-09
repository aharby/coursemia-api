<?php

namespace App\Modules\Countries\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ListCountriesIndex extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->translated_title,
            "country_code" => $this->country_code,
            "flag" => image($this->flag, 'large'),
        ];
    }
}
