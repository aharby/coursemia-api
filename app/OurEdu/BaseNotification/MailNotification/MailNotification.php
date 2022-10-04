<?php

namespace App\OurEdu\BaseNotification\MailNotification;

use App\OurEdu\BaseNotification\MailNotification\MailNotificationInterface;
use App\OurEdu\BaseNotification\MailNotification\NotificationMailTemplate\NotificationMailTemplate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailNotification implements MailNotificationInterface
{
    /**
     * @param array $notification
     * @param array $emails
     */
    public function send(array $notification, array $emails)
    {
        try {

            $notification['data']['lang'] = $notification['lang'] ?? App::getLocale();
            if (env('MAIL_ENABLE', 1)) {
                $emails = array_filter($emails,
                    function ($v) {
                        return filter_var($v, FILTER_VALIDATE_EMAIL);
                    });
                if (count($emails) > 0) {
                    Mail::to($emails)
                        ->send(new NotificationMailTemplate($notification['user_type'],
                            $notification['view'],
                            $notification['data'],
                            $notification['subject']));
                }

            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
