<?php

namespace App\Modules\Countries\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ListCountriesIndex extends JsonResource
{
    public function toArray($request)
    {
        $checkStart = substr($this->flag, 0, 5);
        if ($checkStart == 'https'){
            $flag = $this->flag;
        }else{
            $flag = image($this->flag, 'large');
        }
        return [
            "id" => $this->id,
            "title" => $this->translated_title,
            "country_code" => $this->country_code,
            "flag" => $flag,
        ];
    }
}
