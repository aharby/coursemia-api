<?php


namespace App\OurEdu\Exams\Repository\ExamQuestionAnswer;


use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;

interface ExamQuestionAnswerRepositoryInterface
{
    /**
     * @param array $data
     * @return ExamQuestionAnswer|null
     */
    public function create(array $data): ?ExamQuestionAnswer;

    public function deleteQuestionAnswers(ExamQuestion $examQuestion,int $studentId);
}
