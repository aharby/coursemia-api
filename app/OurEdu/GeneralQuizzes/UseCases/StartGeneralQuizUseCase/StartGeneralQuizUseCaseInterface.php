<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase;

interface StartGeneralQuizUseCaseInterface
{
    /**
     * @param int $quizId
     * @param int $studentId
     */
    public function startQuiz(int $quizId);

}
