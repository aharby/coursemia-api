<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;

interface GeneralQuizQuestionUseCaseInterface
{
    public function addQuestion(GeneralQuiz $generalQuiz, $data);
    public function reviewEssay(GeneralQuiz $generalQuiz,GeneralQuizStudentAnswer $answer,$data);
}
