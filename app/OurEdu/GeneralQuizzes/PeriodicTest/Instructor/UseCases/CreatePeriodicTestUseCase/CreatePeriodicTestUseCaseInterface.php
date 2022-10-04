<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\CreatePeriodicTestUseCase;

interface CreatePeriodicTestUseCaseInterface
{
    public function createPeriodicTest(array $data): array;
}
