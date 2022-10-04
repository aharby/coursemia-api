<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifySupervisorAboutAbsentInstructor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var VCRSession
     */
    private $vcrSession;
    /**
     * @var NotifierFactoryInterface
     */
    private $notifierFactory;

    /**
     * Create a new job instance
     * @param VCRSession $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
        if ($this->vcrSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION) {
            $checkPresence = VCRSessionPresence::where('vcr_session_id', $this->vcrSession->id)
                ->where('user_role', UserEnums::SCHOOL_INSTRUCTOR)->first();
            if (!$checkPresence) {
                $this->notifyInstructorSupervisor($this->vcrSession->instructor);
            }
        }
    }

    /**
     * @param $sessionInstructor
     * @return int
     */
    private function notifyInstructorSupervisor($sessionInstructor)
    {
        $vcrSession = $this->vcrSession;
        if ($sessionInstructor->schoolInstructorBranch && $supervisor = $sessionInstructor->schoolInstructorBranch->supervisor) {
            $classroomSession = ClassroomClassSession::find($vcrSession->classroom_session_id);
            $url = env('APP_URL') . "/{$supervisor->language}/" . getDynamicLink(DynamicLinksEnum::NOTIFY_INSTRUCTOR_SUPERVISOR_ABSENT,
                    [
                        'classroom_session_id' => $vcrSession->classroom_session_id
                    ]);

                    
            $notificationData = [
                'users'                 => collect([$supervisor]),
                NotificationEnums::MAIL => [
                    'user_type' => UserEnums::SCHOOL_SUPERVISOR,
                    'data'      => ['url' => $url, 'lang' => $supervisor->language],
                    'subject'   => trans('notification.instructor_did_not_attend', [], $supervisor->language),
                    'view'      => 'instructorSessionAbsence'
                ],
                NotificationEnums::FCM  => [
                    'data' => [
                        'title' => buildTranslationKey('notification.instructor_did_not_attend'),
                        'body'  => buildTranslationKey('notification.instructor_did_not_attend'),
                        'url'   => $url,
                        'data'  => [
                            'screen_type'           => NotificationEnum::NOTIFY_SUPERVISOR_ABOUT_SCHOOL_INSTRUCTOR_ABSENCE,
                            'class_room_session_id' => $vcrSession->classroom_session_id
                        ],
                    ],
                ]
            ];

            $this->notifierFactory->send($notificationData);
            TrackedVCRNotification::create(
                [
                    'vcr_session_id'   => $vcrSession->id,
                    'vcr_session_type' => $vcrSession->vcr_session_type,
                    'user_id'          => $sessionInstructor->id,
                    'user_role'        => $sessionInstructor->type
                ]
            );
        }
        return 0;
    }
}
