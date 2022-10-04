<?php


namespace App\OurEdu\Exams\UseCases\ExamTakeLikeUseCase;


interface ExamTakeLikeUseCaseInterface
{
    public function create($examID , $studentID);
}
