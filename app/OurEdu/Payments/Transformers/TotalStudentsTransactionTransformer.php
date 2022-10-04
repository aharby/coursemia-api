<?php

namespace App\OurEdu\Payments\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class TotalStudentsTransactionTransformer extends TransformerAbstract
{
    public function transform($totalTransaction)
    {
        $totalSpending = $totalTransaction->total_spending > 0 ? round($totalTransaction->total_spending,2) : 0;
        return [
            'id' => Str::uuid(),
            'total_spending' => (float)$totalSpending,
            'wallet_amount' => (float)$totalTransaction->wallet_amount,
            'total_transfer' => (float)$totalSpending + $totalTransaction->wallet_amount,
            'currency_code' => (string)$totalTransaction->currency_code,
        ];
    }
}
