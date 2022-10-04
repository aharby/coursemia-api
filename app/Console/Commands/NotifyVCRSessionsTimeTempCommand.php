<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotifySupervisorAboutAbsentInstructor;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyVCRSessionsTimeTempCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vcrSession:notify-session-temp {sessionArr}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vcrSession:notify-session-temp {sessionArr}';

    /**
     * Create a new command instance.
     * @return void
     */

    // local variables
    private $VCRSessionParticipantsRepo;
    private $VCRSessionRepo;
    private $notifierFactory;

    public function __construct(
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository,
        NotifierFactoryInterface $notifierFactory,
        VCRSessionRepositoryInterface $VCRSessionRepo
    )
    {
        parent::__construct();
        $this->VCRSessionParticipantsRepo = $VCRSessionParticipantsRepository;
        $this->notifierFactory = $notifierFactory;
        $this->VCRSessionRepo = $VCRSessionRepo;
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $sessionArr = $this->argument('sessionArr');
        $sessionArr = explode(',', $sessionArr);

        // school vcr sessions
        $schoolVCRSessions = VCRSession::whereIn('id', $sessionArr)
            ->with(['instructor','classroom'])
            ->where('vcr_session_type', VCRSessionEnum::SCHOOL_SESSION)
            ->get();

        if (!$schoolVCRSessions->isEmpty()) {
            $this->schoolsVCRSessions($schoolVCRSessions);
        }

        // courses vcr sessions
        $coursesVCRSessions = VCRSession::query()
            ->whereIn('id', $sessionArr)
            ->whereIn('vcr_session_type', [VCRSessionEnum::COURSE_SESSION_SESSION, VCRSessionEnum::LIVE_SESSION_SESSION])
            ->whereHas("courseSession", function ($query) {
                $query->whereHas("course", function ($quer) {
                    $quer->where("is_active", "=", 1);
                });
                $query->where("status", "!=", CourseSessionEnums::CANCELED);
            })
            ->get();
        if (!$coursesVCRSessions->isEmpty()) {
            $this->coursesVCRSessions($coursesVCRSessions);
        }
        return 0;
    }

    private function schoolsVCRSessions($schoolVCRSessions)
    {
        foreach ($schoolVCRSessions as $vcrSession) {
            try {

                $isSpecial = (bool)$vcrSession->classroom->is_special;
                $toBeNotifiedStudents = $this->VCRSessionRepo
                    ->getUnNotifiedClassroomStudents($vcrSession->classroom_id,$isSpecial);

                $this->notifyStudents($toBeNotifiedStudents, $vcrSession);

                NotifySupervisorAboutAbsentInstructor::dispatch($vcrSession)
                    ->delay((new Carbon($vcrSession->time_to_start))->addMinutes(5));
                FinishVCRSessionJob::dispatch($vcrSession)
                    ->delay((new Carbon($vcrSession->time_to_end))->addMinutes(5));
            } catch (\Throwable $e) {
                Log::error($e);
            }

        }
    }

    private function notifyStudents($studentsUsers, $vcrSession, $canNotifyInstructor = true)
    {
        NotificationStudentsJob::dispatch($studentsUsers, $vcrSession, $canNotifyInstructor);
    }

    private function coursesVCRSessions($coursesVCRSessions)
    {
        foreach ($coursesVCRSessions as $vcrSession) {
            $alreadyNotifiedStudents = TrackedVCRNotification::where('vcr_session_id', $vcrSession->id)
                ->where('user_role', UserEnums::STUDENT_TYPE)
                ->pluck('user_id')->toArray();

            $toBeNotifiedStudents = $this->VCRSessionParticipantsRepo
                ->getSessionStudentParticipants($vcrSession->id, $alreadyNotifiedStudents);

            $canNotifyInstructor = !TrackedVCRNotification::where('vcr_session_id', $vcrSession->id)
                ->where('user_role', UserEnums::INSTRUCTOR_TYPE)->first();

            $this->notifyStudents($toBeNotifiedStudents, $vcrSession, $canNotifyInstructor);
        }
    }
}
