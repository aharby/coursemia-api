<?php


namespace App\OurEdu\VCRSchedules\Observers;


use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotifySupervisorAboutAbsentInstructor;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreateVCRSessionObserver
{

    private $VCRSessionParticipantsRepo;
    private $VCRSessionRepo;
    private $notifierFactory;

    public function __construct(
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository,
        NotifierFactoryInterface $notifierFactory,
        VCRSessionRepositoryInterface $VCRSessionRepo
    )
    {
        $this->VCRSessionParticipantsRepo = $VCRSessionParticipantsRepository;
        $this->notifierFactory = $notifierFactory;
        $this->VCRSessionRepo = $VCRSessionRepo;
    }

    public function created(VCRSession $VCRSession)
    {
        if ( $VCRSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION &&
            (new Carbon($VCRSession->time_to_start))->isBetween(now()->subMinute(), now()->addMinutes(29))
        )
        {
            $isSpecial = (bool)$VCRSession->classroom->is_special;
            $toBeNotifiedStudents = $this->VCRSessionRepo
                ->getUnNotifiedClassroomStudents($VCRSession->classroom_id,$isSpecial);
            $this->notifyStudents($toBeNotifiedStudents, $VCRSession);

            NotifySupervisorAboutAbsentInstructor::dispatch($VCRSession)
                ->delay((new Carbon($VCRSession->time_to_start))->addMinutes(5));
            FinishVCRSessionJob::dispatch($VCRSession)->onQueue('sessions')
                ->delay((new Carbon($VCRSession->time_to_end))->addMinutes(5));
            $VCRSession->update(['is_notified'=>1]);
        }
    }

    private function notifyStudents($studentsUsers, $vcrSession)
    {
        NotificationStudentsJob::dispatch($studentsUsers, $vcrSession)->onQueue('sessions')->delay($vcrSession->time_to_start);
    }
}
