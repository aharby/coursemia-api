<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\SatisficationRatingUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface SatisficationRatingUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);
}
