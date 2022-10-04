<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface UpdateCourseHomeworkUseCaseInterface
{
    public function updateHomeWork(GeneralQuiz $homework, $data, Course $course): array;
    public function publishCourseHomeWork(GeneralQuiz $courseHomework): array;
    public function deleteCourseHomeWork(GeneralQuiz $courseHomework): array;


}
