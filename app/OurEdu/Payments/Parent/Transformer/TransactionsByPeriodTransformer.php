<?php

namespace App\OurEdu\Payments\Parent\Transformer;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class TransactionsByPeriodTransformer extends TransformerAbstract
{
    public function transform($transactionData)
    {
        return [
            'id' => Str::uuid(),
            'data' => $transactionData
        ];
    }
}
