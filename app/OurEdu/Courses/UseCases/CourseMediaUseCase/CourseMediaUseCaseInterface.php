<?php

namespace App\OurEdu\Courses\UseCases\CourseMediaUseCase;


use App\OurEdu\Courses\Models\Course;

interface CourseMediaUseCaseInterface
{
    public function attache($medias, Course $course);
    public function detach($medias, Course $course);
}
