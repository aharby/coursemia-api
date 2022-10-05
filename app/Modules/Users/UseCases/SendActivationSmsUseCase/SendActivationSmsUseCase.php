<?php


namespace App\Modules\Users\UseCases\SendActivationSmsUseCase;

use App\Modules\Users\User;
use App\Modules\BaseNotification\Enums\NotificationEnums;
use App\Modules\BaseNotification\NotifierFactory\NotifierFactoryInterface;

class SendActivationSmsUseCase implements SendActivationSmsUseCaseInterface
{
    private $notifierFactory;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    public function send(User $user) {
        $user->otp = $user->confirm_token ?? rand(1000, 9999);
        $user->save();


        $notificationData = [
            'users' => collect([$user]),
            'sms' => [
                'message' => trans('app.Activate Code', [
                    'otp' => $user->otp,
                ], $user->language),
            ],
        ];



        $this->notifierFactory->send($notificationData);
    }
}
