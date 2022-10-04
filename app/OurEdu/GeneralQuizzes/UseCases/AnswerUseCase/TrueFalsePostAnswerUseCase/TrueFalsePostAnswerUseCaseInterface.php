<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

interface TrueFalsePostAnswerUseCaseInterface
{
    /**
     * @param GeneralQuizRepository $examRepository
     * @param GeneralQuizQuestionBank $examQuestion
     * @param Collection $answers
     * @return mixed
     */
    public function postAnswer(GeneralQuizRepository $generalQuizRepository,GeneralQuizQuestionBank $questionBank, Collection $answers);
}
