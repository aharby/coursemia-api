<?php

namespace App\Modules\BaseApp\Providers;

use App\Modules\BaseNotification\FcmNotification\FcmNotification;
use App\Modules\BaseNotification\FcmNotification\FcmNotificationInterface;
use App\Modules\BaseNotification\MailNotification\MailNotificationInterface;
use App\Modules\BaseNotification\NotifierFactory\NotifierFactory;
use App\Modules\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\Modules\BaseNotification\MailNotification\MailNotification;
use App\Modules\BaseNotification\SmsNotification\SmsNotification;
use App\Modules\BaseNotification\SmsNotification\SmsNotificationInterface;
use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
//        $this->app->bind(
//            NotifierFactoryInterface::class,
//            NotifierFactory::class
//        );
//
//        $this->app->bind(
//            SmsNotificationInterface::class,
//            SmsNotification::class
//        );
//
//        $this->app->bind(
//            FcmNotificationInterface::class,
//            FcmNotification::class
//        );
//
//        $this->app->bind(
//            MailNotificationInterface::class,
//            MailNotification::class
//        );
    }
}
