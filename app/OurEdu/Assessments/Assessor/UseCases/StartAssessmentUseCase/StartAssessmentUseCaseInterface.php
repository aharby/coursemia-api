<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase;

interface StartAssessmentUseCaseInterface
{
    /**
     * @param int $assessmentId
     * @param int $assesseeId
     */
    public function startAssessment(int $assessmentId,int $assesseeId);

}
