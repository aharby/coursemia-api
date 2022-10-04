<?php

namespace App\OurEdu\VCRSchedules\Jobs;


use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCaseInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class VCRRequestWaitingDurationValidationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function __construct(public int $vcrSessionID)
    {
    }

    public function handle(VCRRequestUseCaseInterface $vcrRequestUseCase)
    {
        DB::transaction(
            function () use ($vcrRequestUseCase) {
                $vcrSession = VCRSession::query()
                    ->with(['student', 'vcrRequest'])
                    ->lockForUpdate()
                    ->findOrFail($this->vcrSessionID);
                // in case instructor didn't start the session
                if ($vcrSession->status == VCRSessionsStatusEnum::ACCEPTED) {
                    $vcrRequestUseCase->validateRequestWaitingDuration($vcrSession);
                }
            }
        );
    }
}
