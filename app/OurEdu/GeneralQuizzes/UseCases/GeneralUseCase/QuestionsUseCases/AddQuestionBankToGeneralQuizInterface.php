<?php
namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface  AddQuestionBankToGeneralQuizInterface
{

    public function addQuestions(GeneralQuiz $generalQuiz, $data);

    public function getQuestionPublicStatus(GeneralQuiz $generalQuiz, $question);

    public function cloneQuestions (GeneralQuiz $generalQuiz, $questionIds);
}
