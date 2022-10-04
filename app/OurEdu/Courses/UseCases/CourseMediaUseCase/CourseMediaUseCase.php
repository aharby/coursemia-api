<?php

namespace App\OurEdu\Courses\UseCases\CourseMediaUseCase;

use App\OurEdu\Courses\Models\Course;
use Carbon\Carbon;

class CourseMediaUseCase implements CourseMediaUseCaseInterface
{

    function attache($medias, Course $course)
    {
        $response = [];
        if (Carbon::now() > $course->end_date) {
            $response['status'] = 422;
            $response['title'] = 'The course is already ended';
            $response['detail'] = trans('api.The course is already ended');

            return $response;
        }

        foreach ($medias as $media) {
            moveGarbageMedia($media->getId(), $course->media(), 'courses');
        }

        $response['status'] = 200;
        $response['title'] = 'media attached successfully';
        $response['detail'] = trans('api.media attached successfully');

        return $response;
    }

    public function detach($medias, Course $course)
    {
        foreach ($medias as $media) {
            deleteMedia($media->getId(), $course->media(), 'courses');
        }
    }
}
