<?php

declare(strict_types=1);

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionParticipant;

interface VCRSessionParticipantsRepositoryInterface
{

    public function findOrFail(int $id): ?VCRSessionParticipant;

    public function create(array $data): VCRSessionParticipant;

    public function insert(array $data);

    public function getSessionStudentParticipants(int $sessionId, $notifiedUsers = null);

}
