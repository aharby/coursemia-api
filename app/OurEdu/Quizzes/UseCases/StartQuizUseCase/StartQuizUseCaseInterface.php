<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\UseCases\StartQuizUseCase;

use App\OurEdu\Quizzes\Quiz;

interface StartQuizUseCaseInterface
{
    /**
     * @param $quizId
     * @return array
     */
    public function startQuiz($quizId): array;
}
