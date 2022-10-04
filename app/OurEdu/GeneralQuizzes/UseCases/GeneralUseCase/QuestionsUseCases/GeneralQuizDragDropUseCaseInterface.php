<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\User;

interface GeneralQuizDragDropUseCaseInterface
{
    public function addQuestion(GeneralQuiz $generalQuiz, $data);
}
