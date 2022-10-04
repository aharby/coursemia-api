<?php

namespace App\OurEdu\Payments\Transformers;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Payments\PaymentTransaction;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class TransactionDetailsTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [

    ];

    public function transform($transaction)
    {
          $currencyCode = $transaction->receiver->student->educationalSystem->country->currency ?? '';

           $transformedData = [
           'id' => $transaction->id,
           'child' => (string)$transaction->receiver ? $transaction->receiver->name : '',
           'father' => (string)$transaction->sender ? $transaction->sender->name : '',
           'amount' => (float) $transaction->amount . " " . $currencyCode,
         ];
        return $transformedData;
    }

}
