<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\DragDropPostAnswerUseCase;


use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use Swis\JsonApi\Client\Collection;

interface DragDropPostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param array $answers
     * @return mixed
     */
    public function postAnswer(ExamRepository $examRepository,ExamQuestion $examQuestion, Collection $answers);
}
