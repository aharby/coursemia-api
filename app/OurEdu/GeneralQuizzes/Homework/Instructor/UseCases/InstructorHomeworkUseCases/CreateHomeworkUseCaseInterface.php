<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases;

interface CreateHomeworkUseCaseInterface
{
    public function createHomeWork(array $data): array;
}
