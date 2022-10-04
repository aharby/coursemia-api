<?php


namespace App\OurEdu\Users\UseCases\SendActivationMailUseCase;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\Auth\Enum\ActivateAccountEnum;
use Illuminate\Support\Str;

class SendActivationMailUseCase implements SendActivationMailUseCaseInterface
{
    private $notifierFactory;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    public function send($user,string $email = null) {
        $user->confirm_token = rand(1000, 9999);
        $user->save();
        $code = $user->confirm_token;

        
        $notificationData = [
            'users' => collect([$user]),
            'mail' => [
                'user_type' => $user->type,
                'data'=> ['code' => $code, 'lang' => $user->language],
                'subject' => trans('app.Activate Account', [],$user->language),
                'view' => 'activateAccountEmail'
            ],

        ];

        $this->notifierFactory->send($notificationData);
    }
}
