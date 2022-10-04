<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase;

interface FinishGeneralQuizUseCaseInterface
{
    /**
     * @param int $quizId
     * @param int $studentId
     */
    public function finishGeneralQuiz(int $generalQuizId , int $studentId);

}