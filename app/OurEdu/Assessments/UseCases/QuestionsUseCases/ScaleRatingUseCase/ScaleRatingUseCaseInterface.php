<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\ScaleRatingUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface ScaleRatingUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
