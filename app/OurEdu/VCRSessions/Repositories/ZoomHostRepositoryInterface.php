<?php

namespace App\OurEdu\VCRSessions\Repositories;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\Models\ZoomHost;

interface ZoomHostRepositoryInterface
{
    public function getAvailableHost(VCRSession $VCRSession): ?ZoomHost;

    public function freeUsedHost(VCRSession $VCRSession): void;
}
