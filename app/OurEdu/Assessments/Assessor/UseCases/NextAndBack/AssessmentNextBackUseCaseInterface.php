<?php

namespace App\OurEdu\Assessments\Assessor\UseCases\NextAndBack;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;

interface AssessmentNextBackUseCaseInterface
{
    public function nextOrBackQuestion(int $assessmentId, int $assesseeId,  int $page);

    public function getQuestions(Assessment $assessment, AssessmentUser $userAssessment, int $page, int $perPage = null);
}
