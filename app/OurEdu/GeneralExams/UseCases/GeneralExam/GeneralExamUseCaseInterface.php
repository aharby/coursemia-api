<?php

namespace App\OurEdu\GeneralExams\UseCases\GeneralExam;

use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;

interface GeneralExamUseCaseInterface
{
    public function updateQuestions($exam, $data);

    public function generalExamStudent($examId);
}
