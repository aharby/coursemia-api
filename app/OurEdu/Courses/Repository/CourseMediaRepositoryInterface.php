<?php

declare(strict_types=1);

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseMedia;

interface CourseMediaRepositoryInterface
{

    /**
     * @param Course|null $course
     * @return mixed
     */
    public function getInstructorCoursesMedia(Course $course = null);

    /**
     * @param Course|null $course
     * @return mixed
     */
    public function getStudentCoursesMedia(Course$course = null);

    /**
     * @param CourseMedia $courseMedia
     * @return CourseMedia|null
     */
    public function toggleStatus(CourseMedia $courseMedia): ?CourseMedia;
}
