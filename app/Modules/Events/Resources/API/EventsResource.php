<?php

namespace App\Modules\Events\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class EventsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = null;
        if (isset($this->image)){
            $image = image($this->image, 'large');
        }
        return [
            'id' => $this->id,
            'title' => $this->translate(App::getLocale())->title,
            'image' => $image,
            'extra_info' => $this->event_url,
        ];
    }
}
