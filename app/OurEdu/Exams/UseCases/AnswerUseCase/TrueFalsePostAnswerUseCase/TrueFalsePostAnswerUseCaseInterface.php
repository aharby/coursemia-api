<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase;


use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use Swis\JsonApi\Client\Collection;

interface TrueFalsePostAnswerUseCaseInterface
{
    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param Collection $answers
     * @return mixed
     */
    public function postAnswer(ExamRepository $examRepository,ExamQuestion $examQuestion, Collection $answers);
}
