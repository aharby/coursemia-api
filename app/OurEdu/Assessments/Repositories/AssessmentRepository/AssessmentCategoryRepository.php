<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentCategory;

class AssessmentCategoryRepository implements AssessmentCategoryRepositoryInterface
{

    public function __construct(private AssessmentCategory $assessmentCategory)
    {
    }

    public function create($data)
    {
       return  $this->assessmentCategory->create($data);
    }

    public function update(AssessmentCategory $assessmentCategory,$data)
    {
        return $assessmentCategory->update($data);
    }

    public function delete(AssessmentCategory $assessmentCategory)
    {
        return $assessmentCategory->delete();
    }
}
