<?php


namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;

interface AssessmentQuestionRepositoryInterface
{

    public function create(array $data);

    public function updateOrCreate(int $assessmentId,int $questionId,array $data);

    public function findAssessmentQuestion(Assessment $assessment, int $questionId);

    public function findOrFail($questionId): ?AssessmentQuestion;

    public function delete(Assessment $assessment, AssessmentQuestion $assessmentQuestion);
    
    public function updateOrCreateAnswer($question, $data);
}
