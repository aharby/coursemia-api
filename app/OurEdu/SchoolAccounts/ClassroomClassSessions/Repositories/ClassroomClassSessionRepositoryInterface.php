<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories;

use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;

use App\OurEdu\SchoolAccounts\Classroom;

interface ClassroomClassSessionRepositoryInterface
{
    public function getClassroomSessions(Classroom $classroom);
    public function findOrFail(int $id): ?ClassroomClassSession;

}
