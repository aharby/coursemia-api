<?php


namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\AssessmentPointsRate;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentPointsRateRepositoryInterface
{

    /**
     * @param $data
     * @return AssessmentPointsRate
     */
    public function create($data): AssessmentPointsRate;

    /**
     * @return bool
     */
    public function delete(): bool;


    /**
     * @param $assessmentId
     * @return Collection|null
     */
    public function index($assessmentId): ?Collection;
}
