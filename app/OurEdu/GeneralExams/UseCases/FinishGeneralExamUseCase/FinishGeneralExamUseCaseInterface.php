<?php


namespace App\OurEdu\GeneralExams\UseCases\FinishGeneralExamUseCase;

interface FinishGeneralExamUseCaseInterface
{
    /**
     * @param int $examId
     * @param int $studentID
     */
    public function finishExam(int $examId , int $studentID);

}
