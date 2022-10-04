<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface CloneQuestionsUseCaseInterface
{
    public function clone (GeneralQuiz $generalQuiz,$questions);
}
