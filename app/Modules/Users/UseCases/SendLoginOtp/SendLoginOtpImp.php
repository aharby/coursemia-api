<?php

namespace App\Modules\Users\UseCases\SendLoginOtp;

use App\Modules\BaseApp\Enums\DynamicLinksEnum;
use App\Modules\BaseApp\Enums\DynamicLinkTypeEnum;
use App\Modules\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\Modules\Notifications\Enums\NotificationEnum;
use App\Modules\Users\User;
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
