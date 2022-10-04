<?php

namespace App\OurEdu\Payments\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Payments\Models\Order;
use App\OurEdu\Users\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'user'
    ];

    public function transform(Order $order)
    {
        $transformerDatat = [
            'id' => (int) $order->id,
            'user_id' => (int) $order->user_id,
            'order_key' => (string) $order->order_key,
            'payment_method' => (string) $order->payment_method,
            'amount' => (float) $order->amount,
        ];

        return $transformerDatat;
    }

    public function includeUser(Order $order)
    {
        if ($order->user) {
            return $this->item($order->user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }
}
