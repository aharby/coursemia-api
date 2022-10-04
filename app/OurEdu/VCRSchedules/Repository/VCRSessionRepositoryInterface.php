<?php

declare(strict_types=1);

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\VCRSchedules\Models\VCRSession;

interface VCRSessionRepositoryInterface
{

    public function findOrFail(int $id): ?VCRSession;

    /**
     * @param string $type
     * @param int $id
     * @return VCRSession|null
     */
    public function findOrFailWhereType(string $type, int $id): ?VCRSession;

    public function create(array $data): VCRSession;

    public function setVCRSession($VCRSession);

    public function update(int $id, array $data): bool;

    public function findVCRSessionByCourseSession(int $courseId, int $sessionId): VCRSession;

    public function getInstructorSessions(int $instructorId);

    public function getSessionParticipants(VCRSession $VCRSession);

    public function getSessionInstructor(int $sessionId);

    public function getUnNotifiedClassroomStudents(int $classroomId,$isSpecialClassroom);
}
