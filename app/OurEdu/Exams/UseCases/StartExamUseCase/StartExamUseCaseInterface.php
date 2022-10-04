<?php


namespace App\OurEdu\Exams\UseCases\StartExamUseCase;


interface StartExamUseCaseInterface
{
    /**
     * @param int $examId
     */
    public function startExam(int $examId);

    public function startInstructorCompetition(int $competitionId);

}
