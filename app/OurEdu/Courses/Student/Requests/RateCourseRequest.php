<?php

namespace App\OurEdu\Courses\Student\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Courses\Enums\CourseEnums;

class RateCourseRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.rating' => ['required', 'numeric', 'min:0', 'max:' . CourseEnums::TOTAL_STARS],
            'attributes.comment' => ['required', 'max:150'],
        ];
    }
}
