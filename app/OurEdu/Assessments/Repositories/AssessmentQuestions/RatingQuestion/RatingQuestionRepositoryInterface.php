<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion;


use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingOptions;
use App\OurEdu\Assessments\Models\Assessment;
interface RatingQuestionRepositoryInterface 
{
    public function findOrFail(int $id): ?AssissmentRatingQuestion;

    public function findQuestionOrFail(int $id) :? AssissmentRatingQuestion;

    public function createQuestion($data);

    public function updateQuestion($questionId, $data);
    
    public function insertMultipleOptions($data);
    
    public function updateOption($optionsId, $data);

    public function getQuestionOptionsIds($questionId);

    public function deleteOptions($questionId, $optionsIds);
}