<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\SatisfactionUseCase;


use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use Swis\JsonApi\Client\Collection;

interface SatisfactionPostAnswerUseCaseInterface
{
    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion,Collection $answers,$questionAnswerData);
}
