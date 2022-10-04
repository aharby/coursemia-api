<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\PostAnswerUseCase;


use Swis\JsonApi\Client\Collection;

interface PostAnswerUseCaseInterface
{
    /**
     * @param int $assessmentId
     * @param int $questionId
     * @param array $answers
     * @return array
     */
    public function postAnswer(int $assessmentId, Collection $data);

}
