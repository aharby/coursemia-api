<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\FinishAssessmentUseCase;

interface FinishAssessmentUseCaseInterface
{
    /**
     * @param int $assessmentId
     */
    public function finishAssessment(int $assessmentId, int $assessorId,int $assesseeId);

}
