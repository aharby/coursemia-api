<?php

namespace App\OurEdu\BaseNotification\NotifierFactory;


use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\FcmNotification\FcmNotification;
use App\OurEdu\BaseNotification\MailNotification\MailNotificationInterface;
use App\OurEdu\BaseNotification\SmsNotification\SmsNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifierFactory implements NotifierFactoryInterface, ShouldQueue
{
    use Queueable;

    private $smsNotification;
    private $mailNotification;
    private $users;

    public function __construct(
        SmsNotificationInterface $smsNotification,
        MailNotificationInterface $mailNotification
    )
    {
        $this->smsNotification = $smsNotification;
        $this->mailNotification = $mailNotification;
    }

    public function send(array $notificationData)
    {
        if (isset($notificationData['users'])) {
            $this->users = $notificationData['users'];
        }

        // FCM Case
        if (isset($notificationData[NotificationEnums::FCM])) {
            Notification::send($notificationData['users'], new FcmNotification($notificationData[NotificationEnums::FCM]['data']));
        }

        // Mail Case
        if (isset($notificationData[NotificationEnums::MAIL])) {
            if (isset($notificationData['emails'])) {
                $emails = $notificationData['emails'];
                if (!is_array($notificationData['emails'])) {
                    $emails = [$notificationData['emails']];
                }
            } else {
                $emails = $this->users->pluck('email')->toArray();
            }
            $this->mailNotification->send($notificationData[NotificationEnums::MAIL], $emails);
        }

        // SMS Case
        if (isset($notificationData[NotificationEnums::SMS])) {
            $mobiles = $this->users->whereNotNull('mobile')->pluck('mobile')->toArray();
            $this->smsNotification->send($notificationData[NotificationEnums::SMS], $mobiles);
        }
    }
}
