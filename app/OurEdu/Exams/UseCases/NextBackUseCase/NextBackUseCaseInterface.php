<?php


namespace App\OurEdu\Exams\UseCases\NextBackUseCase;

interface NextBackUseCaseInterface
{
    /**
     * @param int $examId
     */
    public function nextOrBackQuestion(int $examId, int $page);

}
