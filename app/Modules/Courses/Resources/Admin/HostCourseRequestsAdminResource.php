<?php

namespace App\Modules\Courses\Resources\Admin;

use App\Modules\BaseApp\Enums\BaseEnum;
use App\Modules\Courses\Models\CourseUser;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class HostCourseRequestsAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "mobile" => $this->mobile,
            "email" => $this->email,
            "about_course" => $this->about_course,
            "country_id" => $this->country_id,
            "speciality_id" => $this->speciality_id,
            "country" => $this->country->translated_title,
            "speciality" => $this->speciality->translated_title,
        ];
    }
}
