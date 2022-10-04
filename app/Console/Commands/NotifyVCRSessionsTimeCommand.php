<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotifySupervisorAboutAbsentInstructor;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\Jobs\VcrSessionProvider;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyVCRSessionsTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vcrSession:notify-session-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vcrSession:notify-session-time';

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
        $now = now()->subMinute()->toDateTimeString();
        $time = now()->addMinutes(29)->toDateTimeString();
        $wheres = [
            ['time_to_start', '>=', $now],
            ['time_to_start', '<=', $time],
        ];

        // school vcr sessions
        $schoolVCRSessions = VCRSession::query()
            ->where($wheres)
            ->with(['instructor','classroom'])
            ->whereNotNull('instructor_id')
            ->whereNotNull('subject_id')
            ->where('is_notified',0)
            ->whereHas('instructor')
            ->where('vcr_session_type', VCRSessionEnum::SCHOOL_SESSION)
            ->orderBy('time_to_start')
            ->get();


        if (!$schoolVCRSessions->isEmpty()) {
            $this->schoolsVCRSessions($schoolVCRSessions);
        }

        // courses vcr sessions
        $coursesVCRSessions = VCRSession::query()
            ->where($wheres)
            ->whereIn('vcr_session_type', [VCRSessionEnum::COURSE_SESSION_SESSION, VCRSessionEnum::LIVE_SESSION_SESSION])
            ->whereHas('instructor')
            ->whereHas("courseSession", function ($query) {
                $query->where("status", "!=", CourseSessionEnums::CANCELED);
                $query->whereHas("course", function ($quer) {
                    $quer->where("is_active", "=", 1);
                });
            })
            ->where('is_notified',0)
            ->get();
        if (!$coursesVCRSessions->isEmpty()) {
            $this->coursesVCRSessions($coursesVCRSessions);
        }
        return 0;
    }

    private function schoolsVCRSessions($schoolVCRSessions)
    {
        foreach ($schoolVCRSessions as $vcrSession) {
            $isSpecial = (bool)$vcrSession->classroom->is_special;
            $toBeNotifiedStudents = $this->VCRSessionRepo
                ->getUnNotifiedClassroomStudents($vcrSession->classroom_id,$isSpecial);

            $canNotifyInstructor = $this->canNotifyInstructor($vcrSession->instructor, $vcrSession);
            $this->notifyStudents($toBeNotifiedStudents, $vcrSession, $canNotifyInstructor);

            NotifySupervisorAboutAbsentInstructor::dispatch($vcrSession)
                ->delay((new Carbon($vcrSession->time_to_start))->addMinutes(5));
            FinishVCRSessionJob::dispatch($vcrSession)
                ->delay((new Carbon($vcrSession->time_to_end)))->onQueue('sessions');
            $vcrSession->update(['is_notified'=>1]);
        }
    }

    private function notifyStudents($studentsUsers, $vcrSession, $canNotifyInstructor)
    {
        NotificationStudentsJob::dispatch($studentsUsers, $vcrSession, $canNotifyInstructor)->delay((new Carbon($vcrSession->time_to_start)))->onQueue('sessions');
    }

    private function canNotifyInstructor($sessionInstructor, $vcrSession)
    {
        if ($sessionInstructor instanceof User) {
            if (in_array($sessionInstructor->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::INSTRUCTOR_TYPE])) {
                return true;
            }
        }
        return false;
    }

    private function coursesVCRSessions($coursesVCRSessions)
    {
        foreach ($coursesVCRSessions as $vcrSession) {
            $alreadyNotifiedStudents = TrackedVCRNotification::where('vcr_session_id', $vcrSession->id)
                ->where('user_role', UserEnums::STUDENT_TYPE)
                ->pluck('user_id')->toArray();

            $toBeNotifiedStudents = $this->VCRSessionParticipantsRepo
                ->getSessionStudentParticipants($vcrSession->id, $alreadyNotifiedStudents);

            $canNotifyInstructor = !TrackedVCRNotification::query()->where('vcr_session_id', $vcrSession->id)
                ->where('user_role', UserEnums::INSTRUCTOR_TYPE)->exists()
                and $this->canNotifyInstructor($vcrSession->instructor, $vcrSession);

            FinishVCRSessionJob::dispatch($vcrSession)
                ->delay((new Carbon($vcrSession->time_to_end)))->onQueue('sessions');

            $this->notifyStudents($toBeNotifiedStudents, $vcrSession, $canNotifyInstructor);
            $vcrSession->update(['is_notified'=>1]);
        }
    }
}
