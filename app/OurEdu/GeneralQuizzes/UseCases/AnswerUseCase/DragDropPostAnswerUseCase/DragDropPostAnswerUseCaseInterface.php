<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\DragDropPostAnswerUseCase;


use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
interface DragDropPostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param array $answers
     * @return mixed
     */
    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers);
}
