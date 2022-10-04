<?php

namespace App\OurEdu\GeneralExams\UseCases\NextAndBack;

interface GeneralExamNextBackUseCaseInterface
{
    public function nextOrBackQuestion(int $examId, int $page);
}
