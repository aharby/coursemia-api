<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;

class SendActivationCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user, public $notificationData = [])
    {

    }


    public function handle(
        NotifierFactoryInterface $notifierFactory
    )
    {
        $this->generateOtp();
        if($this->user->otp) {
            $this->sendNotification($notifierFactory, $this->notificationData);
        }
    }

    private function generateOtp(): void
    {
        $otp = rand(1000, 9999);
        $isExists = User::query()
            ->where("otp", "=", $otp)
            ->exists();

        if ($isExists) {
            $this->generateOtp();
        }

        $this->user->otp = $otp;
        $this->user->save();
    }

    private function sendNotification(NotifierFactoryInterface $notifierFactory, $data)
    {
        if (isset($data['sms'])){
            $notificationData = [
                'users' => $data['users'],
                'sms' => [
                    'message' => trans(
                        $data['sms']['message'],
                        [
                            'otp' => $this->user->otp,
                        ],
                        $this->user->language
                    ),
                ],
            ];

            $notifierFactory->send($notificationData);

        }

        if (isset($data['email'])){
            $notificationData = [
                'users' => $data['users'],
                'mail' => [
                    'user_type' => $data['email']['user_type'],
                    'data' => ['code' => $this->user->otp, 'lang' => $this->user->language],
                    'view' => $data['email']['view'],
                    'subject' => $data['email']['subject'],
                ],
            ];

            $notifierFactory->send($notificationData);

        }
    }

}
