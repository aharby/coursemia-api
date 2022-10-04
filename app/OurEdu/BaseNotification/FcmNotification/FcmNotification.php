<?php

namespace App\OurEdu\BaseNotification\FcmNotification;

use App\Events\UserNotificationsEvent;
use Benwilkins\FCM\FcmMessage;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class FcmNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->firebaseTokens()->count() > 0 ?  ['database', 'fcm'] : ['database'];
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param mixed $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable)
    {
        try {
            $message = new FcmMessage();

            $notificationData = [];


            $notificationData['title'] = displayTranslation($this->data['title'], $notifiable->language);
            $notificationData['body'] = displayTranslation($this->data['body'], $notifiable->language);

            // if (isset($this->data['url'])) {
            //     $notificationData['click_action'] = $this->data['url'];
            // }

            $message->content($notificationData)->priority(FcmMessage::PRIORITY_HIGH);
            if (isset($this->data['url'])) {
                $this->data['data']['click_action'] = $this->data['url'];
            }
            if (isset($this->data['data']) && count($this->data['data'])) {
                $message->data($this->data['data']);
            }

            return $message;
        } catch (ClientException $exception) {
            if ($exception->hasResponse()) {
                if ($exception->getResponse()->getStatusCode() == '400') {
                    Log::channel('slack')->error('FCM 400 Bad Request');
                    Log::channel('slack')->error($exception->getRequest()->getBody()->getContents());
                }
            }
        } catch (Throwable $exception) {
            Log::channel('slack')->error($exception->getMessage());
        }

    }


    /**
     *
     * @param mixed $notifiable
     * @return array $notificationData
     */
    public function toArray($notifiable)
    {
        $notificationData['title'] = displayTranslation($this->data['title'],  $notifiable->language);
        $notificationData['body'] = displayTranslation($this->data['body'], $notifiable->language);
        if (isset($this->data['url'])) {
            $notificationData['url'] = $this->data['url'];
        }
        UserNotificationsEvent::dispatch($notifiable,$notificationData);

        return $this->data;
    }
}
