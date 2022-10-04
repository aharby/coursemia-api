<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\MatrixUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface MatrixUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
