<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\AssessmentPointsRate;
use Illuminate\Database\Eloquent\Collection;

class AssessmentPointsRateRepository implements AssessmentPointsRateRepositoryInterface
{
    public $assessmentPointRate;

    public function __construct(AssessmentPointsRate $assessmentPointRate)
    {
        $this->assessmentPointRate = $assessmentPointRate;
    }

    public function create($data): AssessmentPointsRate
    {
        return $this->assessmentPointRate->create($data);
    }

    public function delete(): bool
    {
        return $this->assessmentPointRate->delete();
    }

    public function index($assessmentId): ?Collection
    {
        return $this->assessmentPointRate
            ->where('assessment_id', $assessmentId)
            ->get();
    }
}
