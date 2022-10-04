<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\UseCases;


interface ClassroomClassSessionUseCaseInterface
{
    public function getSessionStudents($sessionId);
}
