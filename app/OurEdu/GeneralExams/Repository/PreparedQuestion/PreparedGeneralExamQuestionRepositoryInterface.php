<?php

namespace App\OurEdu\GeneralExams\Repository\PreparedQuestion;

interface PreparedGeneralExamQuestionRepositoryInterface
{
    public function create($data);
    public function paginateSectionQuestions($section ,$difficultyLevel, $filters = []);
}
