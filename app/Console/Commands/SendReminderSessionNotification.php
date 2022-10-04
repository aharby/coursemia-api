<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VcrReminder;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionParticipant;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminderSessionNotification extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'send:prepare';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'vcrSession:prepare';


    private $notifierFactory;

    /**
     * Create a new command instance.
     * @param NotifierFactoryInterface $notifierFactory
     */
    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        parent::__construct();
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->prepareReminder();
    }

    public function prepareReminder()
    {
        // TODO: fix performance leak
        $vcrReminderIds = VcrReminder::pluck('session_id')->toArray();

        // Add with to eager load the needed children of sessions
        VCRSession::with(['participants', 'participants.user', 'students'])
            ->whereNotIn('id', $vcrReminderIds)
            ->where('status', VCRSessionsStatusEnum::ACCEPTED)
            ->whereRaw("NOW() BETWEEN DATE_SUB(time_to_start, INTERVAL 1 HOUR) and time_to_start")
            ->chunk(10, function ($sessions) {

                /** @var VCRSession $session */
                foreach ($sessions as $session) {
                    $reminders[] = [
                        'user_id'                 => $session->instructor_id,
                        'user_role'               => UserEnums::INSTRUCTOR_TYPE,
                        'session_id'              => $session->id,
                        'session_type'            => $session->vcr_session_type,
                        'room_uuid'               => $session->room_uuid,
                        'user_uuid'               => $session->agora_instructor_uuid,
                        'session_start_date_time' => $session->time_to_start,
                        'session_end_date_time'   => $session->time_to_end,
                    ];


                    /** @var VCRSessionParticipant $participant */
                    foreach ($session->participants as $participant) {
                        $reminders[] = [
                            'user_id'                 => $participant->user_id,
                            'user_role'               => UserEnums::STUDENT_TYPE,
                            'session_id'              => $session->id,
                            'session_type'            => $session->vcr_session_type,
                            'room_uuid'               => $session->room_uuid,
                            'user_uuid'               => $participant->participant_uuid,
                            'session_start_date_time' => $session->time_to_start,
                            'session_end_date_time'   => $session->time_to_end,
                        ];
                    }

                    VcrReminder::insert($reminders);
                    $this->notifyInstructor($session);
                    $this->notifyStudent($session, $session->students);
                    $reminders = [];
                }
            });
    }

    /**
     * @param $session
     * @return int
     */
    public function notifyInstructor(VCRSession $session)
    {
        $instructor = $session->instructor;
        if ($instructor) {
            $notificationData = [
                'users'                => collect([$instructor]),
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.instructor_session_reminder'),
                        'body'  => buildTranslationKey('notification.instructor_session_reminder'),
                        'url'   => null,
                        'data'  => [
                            'screen_type'           => NotificationEnum::INSTRUCTOR_VCR_SESSION,
                            'class_room_session_id' => $session->classroom_session_id
                        ],
                    ],
                ]
            ];

            $this->notifierFactory->send($notificationData);
        }

        return 0;
    }

    /**
     * @param VCRSession $session
     * @param array      $students
     */
    public function notifyStudent(VCRSession $session,$students)
    {
        $student = $session->student;
        if ($student) {
            $notificationData = [
                'users'                => $students,
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.student_session_reminder'),
                        'body'  => buildTranslationKey('notification.student_session_reminder'),
                        'url'   => null,
                        'data'  => [
                            'screen_type'           => NotificationEnum::NOTIFY_STUDENT_VCR_SESSION,
                            'class_room_session_id' => $session->classroom_session_id
                        ],
                    ],
                ]
            ];

            $this->notifierFactory->send($notificationData);
        }
    }


}
