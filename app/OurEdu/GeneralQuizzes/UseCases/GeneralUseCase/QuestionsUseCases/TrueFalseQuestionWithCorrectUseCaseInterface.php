<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface TrueFalseQuestionWithCorrectUseCaseInterface
{
    public function addQuestion(GeneralQuiz $generalQuiz, $data);
}
