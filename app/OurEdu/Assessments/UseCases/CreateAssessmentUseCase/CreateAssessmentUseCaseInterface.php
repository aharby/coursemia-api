<?php


namespace App\OurEdu\Assessments\UseCases\CreateAssessmentUseCase;

interface CreateAssessmentUseCaseInterface
{
    public function createAssessment($data): array;
}
