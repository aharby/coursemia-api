<?php


namespace App\OurEdu\GeneralExams\UseCases\AnswerUseCase\PostAnswerUseCase;


use Swis\JsonApi\Client\Collection;

interface PostAnswerUseCaseInterface
{
    /**
     * @param int $exam
     * @param int $questionId
     * @param array $answers
     * @return array
     */
    public function postAnswer(int $exam,Collection $data);


}
