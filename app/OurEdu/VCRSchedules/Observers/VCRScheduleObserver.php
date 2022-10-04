<?php

namespace App\OurEdu\VCRSchedules\Observers;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Error;
use Illuminate\Support\Facades\Log;

class VCRScheduleObserver
{

    private $notifierFactory;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\OurEdu\VCRSchedules\Models\VCRSchedule  $schedule
     * @return void
     */
    public function created(VCRSchedule $schedule)
    {
        $students = $schedule->subject->students->load('user');

        $data = [
            'users'                  => $students->pluck('user')->filter(),
            'emails'                 => $students->pluck('user.email')->all(),
            NotificationEnums::MAIL => [
                'user_type' => UserEnums::STUDENT_TYPE,
                'data'      => [
                    'url' => env('STUDENT_PORTAL_URL', 'https://student.testenv.tech') . '/en/vcr/student/available-instructors',
                    'schedule' => $schedule->load(['subject', 'workingDays'])
                ],
                'subject'   => trans('notification.vcr_schedule'),
                'view'      => 'vcrSchedule'
            ],
            NotificationEnums::FCM  => [
                'data' => [
                    'title' => buildTranslationKey('notification.vcr_schedule'),
                    'body'  => buildTranslationKey('notification.vcr_schedule_content', ['subject_name' => $schedule->subject->name]),
                    'data'  => [
                        'screen_type' => NotificationEnum::VCR_SCHEDULED,
                        'vcr_schedule_id' => $schedule->id
                    ],
                ]
            ]
        ];

        $this->notifierFactory->send($data);
    }
}
