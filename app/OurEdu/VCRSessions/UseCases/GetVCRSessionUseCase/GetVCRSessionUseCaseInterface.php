<?php


namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrType\GetVcrType;
use Illuminate\Http\Request;

interface GetVCRSessionUseCaseInterface
{
    /**
     * @param Request $request
     * @param $sessionId
     * @param GetVcrType $getGetVcrType
     * @return bool
     */
    public function getVCRSession(VCRSession $vcrSession, GetVcrType $getGetVcrType): bool;
}
