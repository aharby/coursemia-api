<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\SessionPreparationUseCase;


use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Swis\JsonApi\Client\Interfaces\DataInterface;

interface SessionPreparationUseCaseInterface
{
    /**
     * @param DataInterface $data
     * @param int $sessionId
     * @param bool $isPublished
     * @return ClassroomClassSession
     */
    public function save(DataInterface $data, int $sessionId, bool $isPublished): ClassroomClassSession;
}
