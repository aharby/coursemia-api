<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ValueTextCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (isset($this->parent)){
            $parent_name = $this->parent->title_en;
        }else{
            $parent_name = '';
        }
        return [
            'value'             => $this->id,
            'label'              => $this->title_en,
            'parent_category'  => $parent_name
        ];
    }
}
