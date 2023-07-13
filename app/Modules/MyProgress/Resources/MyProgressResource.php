<?php

namespace App\Modules\MyProgress\Resources;

use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Users\Resources\UserResorce;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProgressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
//            'earned_points'             =>
        ];
    }
}
