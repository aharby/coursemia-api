<?php


namespace App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase;

use App\OurEdu\Assessments\Models\Assessment;

interface UpdateAssessmentUseCaseInterface
{
    public function editAssessment(int $assessmentId, $data): array;

    public function publishAssessment(Assessment $assessment);

    public function unpublishAssessment(Assessment $assessment);

    public function delete(Assessment $assessment): void;
}
