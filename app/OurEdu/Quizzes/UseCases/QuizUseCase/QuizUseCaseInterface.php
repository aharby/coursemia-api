<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\UseCases\QuizUseCase;

use App\OurEdu\Quizzes\Quiz;

interface QuizUseCaseInterface
{
    /**
     * @param $data
     * @return array
     */
    public function createQuiz($data): array;

    /**
     * @param $quiz
     * @param $data
     * @return array
     */
    public function editQuiz($quiz, $data): array;

    /**
     * @param $quizId
     * @return array
     */
    public function getQuiz($quizId): array;

    /**
     * @param $quiz
     * @param $data
     * @return array
     */
    public function updateQuizQuestions($quiz, $data);
}
