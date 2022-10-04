<?php


namespace App\OurEdu\Assessments\UseCases\CloneAssessment;


use App\OurEdu\Assessments\Models\Assessment;
use Swis\JsonApi\Client\Item;

interface CloneAssessmentUseCaseInterface
{
    public function cloneAssessment(Assessment $assessment, Item $data) ;
    public function replicatAssessmentQuestionsWithOptions($assessmentQuestions , $assessment , $oldAssessment) ;

}
