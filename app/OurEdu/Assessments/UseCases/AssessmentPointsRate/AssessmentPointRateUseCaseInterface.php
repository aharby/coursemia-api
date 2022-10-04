<?php


namespace App\OurEdu\Assessments\UseCases\AssessmentPointsRate;

use App\OurEdu\Assessments\Models\Assessment;

interface AssessmentPointRateUseCaseInterface
{
    /**
     * Give each number of calculation of assessment points a specific rate 
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @param array $data
     * @return array
     */
    public function createPointRates(Assessment $assessment, array $data): array;

    /**
     * Get all points rate of assessment
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     */
    public function getAssessmentRates(Assessment $assessment);
}
