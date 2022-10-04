<?php


namespace App\OurEdu\GeneralExams\UseCases\StartExamUseCase;


interface StartGeneralExamUseCaseInterface
{
    /**
     * @param int $examId
     * @param int $studentId
     */
    public function startExam(int $examId , int $studentId);

}
