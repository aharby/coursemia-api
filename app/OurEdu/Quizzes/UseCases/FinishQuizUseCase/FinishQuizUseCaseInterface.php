<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\UseCases\FinishQuizUseCase;

use App\OurEdu\Quizzes\Quiz;

interface FinishQuizUseCaseInterface
{
    /**
     * @param int $examId
     * @return array
     */
    public function finishQuiz(int $examId): array;

}
