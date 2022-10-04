<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface GeneralUseCaseInterface
{
    public function delete(GeneralQuiz $generalQuiz);
}
