<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase;


interface QuizQuestionUseCaseInterface
{
    /**
     * @param $quiz
     * @param $data
     * @return array
     */
    public function createOrUpdateQuizQuestions($quiz, $data);
}
