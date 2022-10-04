<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase;

interface ViewAsStudentUseCaseInterface
{
    public function nextOrBackQuestion(int $generalQuizId, int $page) :array;
}
