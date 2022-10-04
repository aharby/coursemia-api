<?php


namespace App\OurEdu\Exams\UseCases\ExamChallengeUseCase;


interface ExamChallengeUseCaseInterface
{
    public function create($examID , $studentID);
}
