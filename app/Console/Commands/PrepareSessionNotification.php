<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\FcmNotification\FcmNotification;
use App\OurEdu\BaseNotification\MailNotification\MailNotificationInterface;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Subscribes\SubscribeCourse;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VcrReminder;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class PrepareSessionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vcrSession:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vcrSession:prepare';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->prepareReminder();
        return 0;

    }

    public function prepareReminder()
    {
        $now = Carbon::now();
        $time = $now->addHours(1)->toDateTimeString();

        $wheres = [
            ['status', '=', VCRSessionsStatusEnum::ACCEPTED],
            ['time_to_start', '>=', Carbon::now()->toDateTimeString()],
            ['time_to_start', '<=', $time],
        ];

        $vcrReminderIds = VcrReminder::pluck('session_id')->toArray();
        VCRSession::whereNotIn('id', $vcrReminderIds)->where($wheres)->chunk(10, function ($sessions) {
            foreach ($sessions as $session) {

                $instructorData = ['user_id' => $session->instructor_id,
                    'user_role' => UserEnums::INSTRUCTOR_TYPE,
                    'session_id' => $session->id,
                    'session_type' => $session->vcr_session_type,
                    'room_uuid' => $session->room_uuid,
                    'user_uuid' => $session->agora_instructor_uuid,
                    'session_start_date_time' => $session->time_to_start,
                    'session_end_date_time' => $session->time_to_end,
                ];

                VcrReminder::create($instructorData);

                $session->participants()->chunk(10, function ($participant) use ($session) {
                    $data[] = ['user_id' => $participant->instructor_id,
                        'user_role' => UserEnums::INSTRUCTOR_TYPE,
                        'session_id' => $session->id,
                        'session_type' => $session->vcr_session_type,
                        'room_uuid' => $session->room_uuid,
                        'user_uuid' => $participant->participant_uuid,
                        'session_start_date_time' => $session->time_to_start,
                        'session_end_date_time' => $session->time_to_end,
                    ];
                    VcrReminder::insert($data);

                });


            }


        });
    }


}
