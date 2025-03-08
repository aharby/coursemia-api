<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class QuestionOfTheDay extends Notification
{
    use Queueable;
    protected $question;

    public function __construct($question)
    {
        $this->question = $question;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData(['question' => $this->question])
            ->setNotification(FcmNotification::create()
                ->setTitle('Question of the Day')
                ->setBody($this->question)
            );
    }
}
