<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\MultipleMatchingPostAnswerUseCase;


use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use Swis\JsonApi\Client\Collection;

interface MultipleMatchingPostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param array $answers
     * @return mixed
     */
    public function postAnswer(ExamRepository $examRepository,ExamQuestion $examQuestion, Collection $answers);
}
