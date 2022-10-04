<?php

namespace App\OurEdu\Courses\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\Courses\Models\SubModels\CourseSession;

class ViewCourseSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function transform(CourseSession $courseSession)
    {
        $transformedData = [
            'id' => (int) $courseSession->id,
            'course_id' => (int) $courseSession->course_id,
            'date' => (string) date("d M Y", strtotime($courseSession->date)),
            'content' => (string) $courseSession->content,
            'start_time' => (string) date("g:iA", strtotime($courseSession->start_time)),
            'end_time' => (string)date("g:iA", strtotime($courseSession->end_time)) ,
        ];

        return $transformedData;
    }
}
