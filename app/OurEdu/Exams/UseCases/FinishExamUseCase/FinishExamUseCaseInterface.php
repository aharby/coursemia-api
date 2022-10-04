<?php


namespace App\OurEdu\Exams\UseCases\FinishExamUseCase;


use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;

interface FinishExamUseCaseInterface
{
    /**
     * @param int $examId
     */
    public function finishExam(int $examId);

    public function finishExamCompetition(int $examId);

    public function finishInstructorCompetition(int $competitionId);

    public function getStudentOrderInCompetition(Exam $exam, Student $student);


}
