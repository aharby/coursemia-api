<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase;


use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
interface MultiChoicePostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param array $answers
     * @return mixed
     */
    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers);
}
