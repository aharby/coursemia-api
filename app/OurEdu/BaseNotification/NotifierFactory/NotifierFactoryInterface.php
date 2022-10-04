<?php

namespace App\OurEdu\BaseNotification\NotifierFactory;

interface NotifierFactoryInterface
{
    /**
     * @param array $notificationData
     */
    public function send(array $notificationData);
}
