<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases;

use App\OurEdu\Courses\Models\Course;

interface CreateCourseHomeworkUseCaseInterface
{
    public function createHomeWork(array $data, Course $course): array;
}
