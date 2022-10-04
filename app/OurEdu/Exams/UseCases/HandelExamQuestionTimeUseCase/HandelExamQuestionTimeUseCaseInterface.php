<?php


namespace App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase;


use App\OurEdu\Exams\Models\ExamQuestion;
use Illuminate\Pagination\LengthAwarePaginator;

interface HandelExamQuestionTimeUseCaseInterface
{
    /**
     * @param LengthAwarePaginator $questions
     * @param $currentQuestionId
     * @return mixed
     */
    public function handleTime(LengthAwarePaginator $questions, $currentQuestionId);

    /**
     * @param ExamQuestion $examQuestion
     * @return mixed
     */
    public function insertStart(ExamQuestion $examQuestion);

    /**
     * @param $examId
     * @return mixed
     */
    public function endAllOpenQuestions($examId);

    /**
     * @param $currentQuestionId
     * @return mixed
     */
    public function endLastExamQuestionTime($currentQuestionId);


}

