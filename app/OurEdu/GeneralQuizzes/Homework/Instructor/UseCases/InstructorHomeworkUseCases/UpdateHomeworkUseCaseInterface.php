<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface UpdateHomeworkUseCaseInterface
{
    public function updateHomeWork(int $homeworkId,$data): array;

    public function publishHomework(GeneralQuiz $homework, bool $force = false): array;
}
