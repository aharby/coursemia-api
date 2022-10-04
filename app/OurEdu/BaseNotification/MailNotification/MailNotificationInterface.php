<?php

namespace App\OurEdu\BaseNotification\MailNotification;

interface MailNotificationInterface
{
    /**
     * @param array $notification
     * @param array $emails
     */
    public function send(array $notification, array $emails);
}
