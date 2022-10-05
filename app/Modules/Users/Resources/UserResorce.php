<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResorce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'profile_image'     => asset($this->photo),
            'token'             => $this->createToken('AccessToken')->accessToken,
            'full_name'         => $this->full_name,
            'country_code'      => $this->country_code,
            'phone_number'      => $this->phone,
            'email_address'     => $this->email,
            'country_id'        => $this->country_id,
            'country_name'      => $this->country->name,
            'is_phone_verified' => (bool) $this->is_verified,
            'referal_code'      => $this->refer_code
        ];
    }
}
