<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\CompletePostAnswerUseCase;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

interface CompletePostAnswerUseCaseInterface
{
    /**
     * @param GeneralQuizRepository $generalQuizRepository
     * @param GeneralQuizQuestionBank $questionBank
     * @param Collection $answers
     * @return mixed
     */
    public function postAnswer(GeneralQuizRepository $generalQuizRepository,GeneralQuizQuestionBank $questionBank, Collection $answers);
}
