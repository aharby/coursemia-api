<?php

namespace App\OurEdu\BaseNotification\SmsNotification;

interface SmsNotificationInterface
{
    /**
     * @param array $notification
     * @param array $mobile
     */
    public function send(
        array $notification,
        array $mobile
    );
}
