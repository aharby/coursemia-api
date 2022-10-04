<?php

namespace App\OurEdu\Users\UseCases\SendLoginOtp;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class SendLoginOtpImp implements SendLoginOtp
{
    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    public function send(User $user)
    {
        $otp = random_int(000000,999999);
        $user->otp = $otp;
        $user->save();


        $notificationData = [
            'users' => collect([$user]),
            'mail' => [
                'user_type' => $user->type,
                'data'=> ['lang' => $user->language,'code' => $otp],
                'subject' => trans('emails.student otp subject', [],$user->language),
                'view' => 'loginOtp'
            ]
        ];

        $this->notifierFactory->send($notificationData);
    }
}
