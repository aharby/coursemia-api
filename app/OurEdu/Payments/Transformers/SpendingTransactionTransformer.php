<?php
namespace App\OurEdu\Payments\Transformers;

use Illuminate\Support\Str;

use League\Fractal\TransformerAbstract;

class SpendingTransactionTransformer extends TransformerAbstract
{
    public function transform($user)
    {
        $totalTransfer = 0;
        $wallet_amount = $user->student ? $user->student->wallet_amount : '' ;
        $currency = $user->student->educationalSystem->country->currency ?? '';
        $totalSpending = $user->children_spent_payment_transactions_sum_amount>0 ? round($user->children_spent_payment_transactions_sum_amount,2):0;
        $totalTransfer = $totalSpending + $wallet_amount;
        $transformedData = [
            'id' => Str::uuid(),
            'name' => (string)$user->name ?? '',
            'total_spending' => (float)$totalSpending,
            'wallet_amount' => (float)$wallet_amount,
            'currency' => (string)$currency,
            'total_transfer' => (float)$totalTransfer
        ];

        return $transformedData;
    }

}
