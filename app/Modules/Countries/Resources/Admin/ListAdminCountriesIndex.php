<?php

namespace App\Modules\Countries\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ListAdminCountriesIndex extends JsonResource
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
            "title_en" => $this->translate('en')->title,
            "title_ar" => $this->translate('ar')->title,
            "country_code" => $this->country_code,
            "flag" => $flag,
            "is_active" => (bool)$this->is_active,
            "status" => $this->is_active ? BaseEnum::ACTIVE : BaseEnum::NOT_ACTIVE,
        ];
    }
}
