<?php

namespace App\OurEdu\Payments\Jobs;

use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use Illuminate\Support\Facades\Log;

class NotifyParentAboutChildPayment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    private PaymentTransaction $transaction;

    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        try{
            $parents = $this->transaction->receiver->parents;
            if ($parents) {
                $notificationData = [
                    'users' => $parents,
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => buildTranslationKey('notification.child_pay_by_visa'),
                            'body' => buildTranslationKey('notification.child_pay_by_visa_body', [
                                'name' => $this->transaction->receiver->name,
                                'product' => $this->transaction->detail?->subscribable?->name
                                    ?? trans('app.vcr_request')
                            ]),
                            'data' => [
                                'payment_for' => $this->transaction->payment_transaction_for,
                                'payment_for_id' => $this->transaction->detail->subscribable_id,
                                'screen_type' => NotificationEnum::NOTIFY_PARENT_ABOUT_VISA_PAYMENT,
                            ],
                            'url' => getDynamicLink(DynamicLinksEnum::STUDENTPAIDBYVISA, [
                                'child_id' => $this->transaction->receiver_id,
                                'portal_url' => env('STUDENT_PORTAL_URL'),
                            ]),
                        ]
                    ]
                ];

                $notifierFactory->send($notificationData);
            }
        }catch (\Exception $exception){
            Log::error("error occurred in NotifyParentAboutChildPayment",[
                'this error occurred in transaction =>'=> $this->transaction,
                'error'=> $exception->getMessage(),
                'line'=> $exception->getLine(),
                'file'=> $exception->getFile(),
            ]);
        }
    }
}
