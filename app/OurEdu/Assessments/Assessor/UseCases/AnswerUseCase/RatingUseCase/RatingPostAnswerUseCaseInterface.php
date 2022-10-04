<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\RatingUseCase;


use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use Swis\JsonApi\Client\Collection;

interface RatingPostAnswerUseCaseInterface
{
    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion,Collection $answers,$questionAnswerData);

}
