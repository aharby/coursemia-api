<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\EssayQuestionUseCase;


use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use Swis\JsonApi\Client\Collection;

interface EssayQuestionPostAnswerUseCaseInterface
{
    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion,Collection $answers,$questionAnswerData);
}
