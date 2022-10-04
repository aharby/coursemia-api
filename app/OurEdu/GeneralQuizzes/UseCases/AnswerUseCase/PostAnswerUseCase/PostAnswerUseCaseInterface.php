<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase;


use Swis\JsonApi\Client\Collection;

interface PostAnswerUseCaseInterface
{
    /**
     * @param int $quizId
     * @param int $questionId
     * @param array $answers
     * @return array
     */
    public function postAnswer(int $quizId,Collection $data);


}
