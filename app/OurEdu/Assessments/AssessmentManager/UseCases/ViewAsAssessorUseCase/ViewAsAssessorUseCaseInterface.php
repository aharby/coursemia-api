<?php

namespace App\OurEdu\Assessments\AssessmentManager\UseCases\ViewAsAssessorUseCase;

interface ViewAsAssessorUseCaseInterface
{
    public function nextOrBackQuestion(int $assessmentID, int $page) :array;
}
