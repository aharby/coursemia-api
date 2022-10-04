<?php

namespace App\OurEdu\Courses\Admin\Listeners\LiveSessions;

use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionCanceled;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyLiveSessionSubscripersOnCancel
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LiveSessionCanceled $event)
    {
        $studentUsers = Student::whereHas('courses', function ($query) use ($event) {
            $query->withoutGlobalScopes()->where('id', $event->liveSession->id);
        })->with('user')->get();

        if (! $studentUsers) {
            return;
        }

        foreach ($studentUsers as $studentUser) {

            $notificationData = [
                'users' => $studentUser->user,
                NotificationEnums::DB => [
                    'data' => [
                        'title'        =>  buildTranslationKey('notification.session Canceled'),
                        'body'         => buildTranslationKey('notification.session_cancellation_notification_body' ,
                            [  'subject_name'=> $event->liveSession->subject->name, 'instructor_name' =>$event->liveSession->instructor->name , 'session_time' =>  $event->liveSession->session->start_time]),
                    ]
                ],
                NotificationEnums::FCM => [
                    'data' => [
                        'title'        =>  buildTranslationKey('notification.session Canceled'),
                        'body'         => buildTranslationKey('notification.session_cancellation_notification_body' ,
                            [  'subject_name'=> $event->liveSession->subject->name, 'instructor_name' =>$event->liveSession->instructor->name , 'session_time' =>  $event->liveSession->session->start_time]),
                    ]
                ]
            ];
            $this->notifierFactory->send($notificationData);
        }
    }
}
