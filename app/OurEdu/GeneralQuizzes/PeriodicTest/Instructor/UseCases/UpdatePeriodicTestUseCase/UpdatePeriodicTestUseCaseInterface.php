<?php

namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface UpdatePeriodicTestUseCaseInterface
{
    public function updatePeriodicTest(int $periodicTestId,$data): array;

    public function publishPeriodicTest(GeneralQuiz $periodicTest): array;

    public function deactivatePeriodicTest(GeneralQuiz $periodicTest);
}
