<?php


namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrType;

use App\OurEdu\BaseNotification\Jobs\NotifyStudentVCRSessionJob;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Instructor\Events\InstructorJoinRequestedSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSchedules\Repository\VCRSessionPresenceRepositoryInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Facades\Auth;

class GetVcrType
{
    private $user;
    /**
     * @var VCRSession
     */
    private VCRSession $vcrSession;
    /**
     * @var VCRSessionPresenceRepositoryInterface
     */
    private VCRSessionPresenceRepositoryInterface $VCRSessionPresenceRepository;

    /**
     * GetVcrType constructor.
     * @param VCRSession $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
        $this->user = Auth::guard('api')->user();
        $this->VCRSessionPresenceRepository = app(VCRSessionPresenceRepositoryInterface::class);
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $vcrPresence = VCRSessionPresence::query()
            ->where('vcr_session_id', $this->vcrSession->id)
            ->where('user_id', $this->user->id)
            ->first();

        if (!$vcrPresence) {
            $vcrPresence = $this->VCRSessionPresenceRepository->create([
                'vcr_session_id' => $this->vcrSession->id,
                'vcr_session_type' => $this->vcrSession->vcr_session_type,
                'user_id' => $this->user->id,
                'user_role' => $this->user->type,
                'entered_at' => now(),
                'session_time_to_start' => $this->vcrSession->time_to_start,
                'session_time_to_end' => $this->vcrSession->time_to_end,
            ]);
        }

        $vcrPresence->left_at = null;
        $vcrPresence = $vcrPresence->save();
        if ($this->vcrSession->status != VCRSessionsStatusEnum::STARTED &&
            $this->user->type == UserEnums::INSTRUCTOR_TYPE ) {
            $this->vcrSession->update([
                'status' => VCRSessionsStatusEnum::STARTED
            ]);
        }
        if($this->vcrSession->vcr_session_type == VCRSessionsTypeEnum::REQUESTED_LIVE_SESSION){
            event(new InstructorJoinRequestedSession($this->vcrSession));
            NotifyStudentVCRSessionJob::dispatch($this->vcrSession,$this->vcrSession->student->user);
        }
        return $vcrPresence;
    }
}
