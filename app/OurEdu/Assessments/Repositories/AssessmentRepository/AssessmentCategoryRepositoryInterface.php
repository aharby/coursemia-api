<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\AssessmentCategory;

interface AssessmentCategoryRepositoryInterface
{
    public function create($data);

    public function update(AssessmentCategory $assessmentCategory,$data);

    public function delete(AssessmentCategory $assessmentCategory);
}
