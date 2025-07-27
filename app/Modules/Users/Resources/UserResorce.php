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
        $flag  = $this->country->flag;

        if(!str_starts_with($this->photo, 'https')){
            $flag = image($flag, 'large');
        }

        return [
            'id'                => $this->id,
            'profile_image'     => asset($this->photo),
            'full_name'         => $this->full_name,
            'country_code'      => $this->country_code,
            'phone_number'      => $this->phone,
            'email_address'     => $this->email,
            'country_id'        => $this->country_id,
            'country_name'      => $this->country->translated_title,
            'country_flag'     =>  $this->country->flag,
            'is_phone_verified' => (bool) $this->is_verified,
            'referral_code'     => $this->refer_code,
            'rank'              => $this->rank,
            'followers'         => $this->followers()->count(),
            'posts'             => $this->posts()->count()
        ];
    }
}
