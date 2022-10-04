<?php


namespace App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase;


use Swis\JsonApi\Client\Collection;

interface AnswerQuizQuestionUseCaseInterface
{
    /**
     * @param int $quizId
     * @param Collection $data
     * @return array
     */
    public function postAnswer(int $quizId,Collection $data);

}
