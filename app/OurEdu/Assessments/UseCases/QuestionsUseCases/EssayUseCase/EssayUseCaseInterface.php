<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\EssayUseCase;


use App\OurEdu\Assessments\Models\Assessment;

interface EssayUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
