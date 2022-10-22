<?php

namespace App\Modules\Users\Resources;

use App\Modules\Users\Models\UserDevice;
use Illuminate\Http\Resources\Json\JsonResource;

class UserConfigurationsResourceResorce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $send_notifications = UserDevice::where('device_id', $request->header('device_id'))->first();
        return [
            'messages_count'            => rand(1,25), // @todo implement messages count
            'follow_requests_count'     => rand(1,25), // @todo implement follow requests count
            'my_cart_count'             => rand(1,25), // @todo implement my cart count
            'send_push_notifications'   => (boolean)$send_notifications->allow_push_notifications
        ];
    }
}
