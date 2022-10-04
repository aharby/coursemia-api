<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice;


use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
interface MultipleChoiceRepositoryInterface 
{
    public function findOrFail(int $id): ?AssessmentMultipleChoiceQuestion;

    public function findQuestionOrFail(int $id) :? AssessmentMultipleChoiceQuestion;

    public function createQuestion($data);

    public function updateQuestion($questionId, $data);
    
    public function insertMultipleOptions($data);
    
    public function updateOption($optionsId, $data);

    public function getQuestionOptionsIds($questionId);

    public function deleteOptions($questionId, $optionsIds);
}