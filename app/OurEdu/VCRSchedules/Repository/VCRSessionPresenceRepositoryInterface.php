<?php

declare(strict_types=1);

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;

interface VCRSessionPresenceRepositoryInterface
{
    public function create(array $data): VCRSessionPresence;
}
