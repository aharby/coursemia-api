<?php

namespace App\OurEdu\Exams\UseCases\FinishExamUseCase;

interface NotifyParentsAboutExamResultUseCaseInterface
{
    public function notifyParents($exam, $studentUser);
}
