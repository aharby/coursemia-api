<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MultipleChoiceUseCase;


use Swis\JsonApi\Client\Collection;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;

interface MultipleChoicePostAnswerUseCaseInterface
{
    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion,Collection $answers,$questionAnswerData);
}
