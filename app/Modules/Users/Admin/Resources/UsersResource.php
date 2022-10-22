<?php

namespace App\Modules\Users\Admin\Resources;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseLecture;
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
        return [
            'id'            => $this->id,
            'fullName'      => $this->full_name,
            'username'      => $this->full_name,
            'email'         => $this->email,
            'avatar'        => asset($this->photo),
            'role'          => 'editor',
            'status'        => $this->is_active ? 'active' : 'not active',
            'verified'      => $this->is_verified ? 'verified' : 'not-verified',
            'country'       => $this->country->translated_title,
            'ability'       => [
                [
                  "action" => 'manage',
                  "subject" => 'all'
                ],
            ]
        ];
    }
}
