<?php

namespace App\OurEdu\Payments\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class PaymentTransactionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'sender',
        'receiver',
    ];

    protected array $availableIncludes = [
        'sender',
        'receiver',
        'actions'
    ];

    public function __construct(public array $payment = [])
    {
    }

    public function transform(PaymentTransaction $transaction)
    {
        $currencyCode = $transaction->receiver->student->educationalSystem->country->currency ?? '';
        return [
            'id' => (int) $transaction->id,
            'order_number' => $transaction->order_number,
            'sender_id' => (int) $transaction->sender_id,
            'amount' => (float) $transaction->amount . " " . $currencyCode,
            'date_time' => $transaction->created_at->format('Y-m-d H:i:s'),
            'receiver_id' => (int) $transaction->receiver_id,
            'payment' => $this->payment,
            'details' => !empty($transaction->sender_id) ? trans('payments.bank transfer') : trans(
                'payments.refund of canceled requested session'
            )
        ];
    }

    public function includeSender(PaymentTransaction $transaction)
    {
        if ($transaction->sender) {
            $params['no_action'] = true;

            return $this->item($transaction->sender, new UserTransformer($params), ResourceTypesEnums::USER);
        }
    }

    public function includeReceiver(PaymentTransaction $transaction)
    {
        if ($transaction->receiver()->exists()) {
            $params['no_action'] = true;

            return $this->item($transaction->receiver, new UserTransformer($params), ResourceTypesEnums::USER);
        }
    }
    public function includeActions(PaymentTransaction $transaction)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.parent.payments.transaction.details',
                [
                    'transaction' => $transaction->id,
                ]
            ),
            'label' => trans('app.details'),
            'method' => 'GET',
            'key' =>  ResourceTypesEnums::PAYMENT_TRANSACTION
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
