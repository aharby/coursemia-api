<?php


namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\UpdateFormativeTestUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface UpdateFormativeTestUseCaseInterface
{
    public function updateFormativeTest(array $data, GeneralQuiz $formativeTest): array;

    public function publishFormativeTest(GeneralQuiz $formativeTest): array;

}
