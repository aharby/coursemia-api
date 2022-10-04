<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\SingleChoicePostAnswerUseCase;


use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
interface SingleChoicePostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param array $answers
     * @return mixed
     */
    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers);
}
