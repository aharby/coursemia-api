<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\NextAndBack;

interface GeneralQuizNextBackUseCaseInterface
{
    public function nextOrBackQuestion(int $generalQuizId, int $page);
}
