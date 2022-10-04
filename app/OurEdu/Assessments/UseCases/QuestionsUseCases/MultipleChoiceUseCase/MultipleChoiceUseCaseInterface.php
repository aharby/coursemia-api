<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\MultipleChoiceUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface MultipleChoiceUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
