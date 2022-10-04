<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneFormativeUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface CloneFormativeUseCaseInterface
{
    public function clone(GeneralQuiz $generalQuiz, $data);
}
