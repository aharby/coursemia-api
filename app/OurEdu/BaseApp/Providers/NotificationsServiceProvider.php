<?php

namespace App\OurEdu\BaseApp\Providers;

use App\OurEdu\BaseNotification\FcmNotification\FcmNotification;
use App\OurEdu\BaseNotification\FcmNotification\FcmNotificationInterface;
use App\OurEdu\BaseNotification\MailNotification\MailNotificationInterface;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\BaseNotification\MailNotification\MailNotification;
use App\OurEdu\BaseNotification\SmsNotification\SmsNotification;
use App\OurEdu\BaseNotification\SmsNotification\SmsNotificationInterface;
use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            NotifierFactoryInterface::class,
            NotifierFactory::class
        );

        $this->app->bind(
            SmsNotificationInterface::class,
            SmsNotification::class
        );

        $this->app->bind(
            FcmNotificationInterface::class,
            FcmNotification::class
        );

        $this->app->bind(
            MailNotificationInterface::class,
            MailNotification::class
        );
    }
}
