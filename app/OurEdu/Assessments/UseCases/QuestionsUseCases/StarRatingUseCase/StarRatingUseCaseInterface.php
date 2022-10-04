<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\StarRatingUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface StarRatingUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
