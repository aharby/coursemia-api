<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\UseCases\NextBackUseCase;

use App\OurEdu\Quizzes\Quiz;

interface NextBackUseCaseInterface
{
    /**
     * @param int $quizId
     * @param int $page
     */
    public function nextOrBackQuestion(int $quizId, int $page);

}
