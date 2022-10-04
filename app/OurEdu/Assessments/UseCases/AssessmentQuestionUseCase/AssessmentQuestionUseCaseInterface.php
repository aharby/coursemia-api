<?php


namespace App\OurEdu\Assessments\UseCases\AssessmentQuestionUseCase;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;

interface AssessmentQuestionUseCaseInterface
{
    public function addQuestion(Assessment $assessment, $data);

    public function deleteQuestion(Assessment $assessment, AssessmentQuestion $assessmentQuestion);
    public function cloneQuestion(AssessmentQuestion $assessmentQuestion, Assessment $assesment);
}
