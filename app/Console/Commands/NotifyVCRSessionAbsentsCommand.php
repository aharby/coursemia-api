<?php

namespace App\Console\Commands;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Console\Command;

class NotifyVCRSessionAbsentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'vcrSession:notify-absents';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'vcrSession:notify-absents';

    /**
     * Create a new command instance.
     * @return void
     */

    // local variables
    private $VCRSessionParticipantsRepo;
    private $notifierFactory;

    public function __construct(
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository,
        NotifierFactoryInterface $notifierFactory
    ) {
        parent::__construct();
        $this->VCRSessionParticipantsRepo = $VCRSessionParticipantsRepository;
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * Execute the console command.
     ***************************************************
     *  this command for watching users presence in VCR
     *  by checking the VCRSessions and VCRSessionPresence tables
     *  and send notifications for the late users
     ***************************************************
     * @return mixed
     */
    public function handle()
    {
        // Check when current time is higher than the start time + 5 minutes and
        // the current time is still less than the end time by these two conditions
        // we will check if a session started 5 minutes ago or more but not if a session ended
        $wheresQuery = '(NOW() BETWEEN DATE_ADD(time_to_start, INTERVAL 5 MINUTE) and time_to_end )';
        $vcrSessions = VCRSession::whereRaw($wheresQuery)
            ->get();

        foreach ($vcrSessions as $vcrSession) {
            // TODO:: getting participants should be generic
            $attendedStudentsIds = VCRSessionPresence::where('vcr_session_id', $vcrSession->id)
                ->where('user_role', UserEnums::STUDENT_TYPE)
                ->pluck('user_id')->toArray();

            // where not notified before
            $notifiedBefore = TrackedVCRNotification::where('vcr_session_id', $vcrSession->id)
                ->pluck('user_id');

            $absentStudentsUsers = $this->VCRSessionParticipantsRepo
                ->getSessionAbsentStudentParticipants($vcrSession->id, $attendedStudentsIds, $notifiedBefore);

            $this->notifyStudentsParents($absentStudentsUsers, $vcrSession);

            if ($vcrSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION) {
                if (! VCRSessionPresence::where('vcr_session_id', $vcrSession->id)
                        ->where('user_role', UserEnums::SCHOOL_INSTRUCTOR)->first() &&
                    ! TrackedVCRNotification::where('vcr_session_id', $vcrSession->id)
                        ->where('user_role', UserEnums::SCHOOL_INSTRUCTOR)->first()) {

                    $this->notifyInstructorSupervisor($vcrSession->instructor, $vcrSession);
                }
            }
        }
    }

    private function notifyStudentsParents($absentStudentsUsers, $vcrSession)
    {
        foreach ($absentStudentsUsers as $studentUser) {
            if (! $studentUser->parents->isEmpty()) {
                $notificationData = [
                    'users'                 => $studentUser->parents,
                    NotificationEnums::MAIL => [
                        'user_type' => UserEnums::PARENT_TYPE,
                        'data'      => ['url' => '', 'lang' => 'ar'],
                        'subject'   => trans('notification.student_did_not_attend', [], 'ar'),
                        'view'      => 'vcrSessionAbsence'
                    ],
                    NotificationEnums::FCM  => [
                        'data' => [
                            'title' => buildTranslationKey('notification.student_did_not_attend'),
                            'body'  => buildTranslationKey('notification.student_did_not_attend'),
                            'data'  => [
                                'screen_type' => NotificationEnum::NOTIFY_PARENT_ABOUT_STUDENT_ABSENCE,
                            ],
                        ]
                    ]
                ];
                $this->notifierFactory->send($notificationData);
                TrackedVCRNotification::create([
                    'vcr_session_id'   => $vcrSession->id,
                    'vcr_session_type' => $vcrSession->vcr_session_type,
                    'user_id'          => $studentUser->id,
                    'user_role'        => $studentUser->type
                ]);
            }
        }
    }

    private function notifyInstructorSupervisor($sessionInstructor, $vcrSession)
    {

        if ($supervisor = $sessionInstructor->schoolInstructorBranch->supervisor) {
            $url = env('APP_URL') . "/{$supervisor->language}/" . getDynamicLink(DynamicLinksEnum::NOTIFY_INSTRUCTOR_SUPERVISOR_ABSENT,
                    ['class_room_session_id' => $vcrSession->classroom_session_id]);

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
            TrackedVCRNotification::create([
                'vcr_session_id'   => $vcrSession->id,
                'vcr_session_type' => $vcrSession->vcr_session_type,
                'user_id'          => $sessionInstructor->id,
                'user_role'        => $sessionInstructor->type
            ]);
        }

        return 0;

    }
}
