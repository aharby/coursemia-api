<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\StudentPeriodicTestTimeUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;

interface  StudentPeriodicTestTimeUseCaseInterface
{

    public function updateStudentPeriodicTestTime(GeneralQuiz $periodicTest,array $data);

    public function getStudentPeriodicTestTimeLeft(GeneralQuiz $periodicTest);
}
