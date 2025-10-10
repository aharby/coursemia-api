<?php

namespace App\Modules\Users\Admin\Resources;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Resources\Admin\CoursesResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $phone = null;

        if($this->country)
            $phone = $this->country_code.$this->phone;

        return [
            'id'            => $this->id,
            'fullName'      => $this->full_name,
            'username'      => $this->full_name,
            'email'         => $this->email,
            'phone'         => $phone,
            'referral_code' => $this->refer_code,
            'courses_bought'=> CoursesResource::collection($this->courses),
            'avatar'        => asset($this->photo),
            'role'          => 'editor',
            'is_active'     => (bool)$this->is_active,
            'devices'       => UserDeviceResource::collection($this->devices),
            'is_phone_verified'      => $this->is_verified,
            'is_email_verified' => $this->hasVerifiedEmail(),
            'country'       => $this->country?->translated_title,
            'ability'       => [
                [
                  "action" => 'manage',
                  "subject" => 'all'
                ],
            ]
        ];
    }
}
