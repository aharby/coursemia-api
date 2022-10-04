<?php

namespace App\OurEdu\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class UrwayCheckout extends Model
{
    protected $fillable = [
        'udf1',
        'response_code',
        'response_message',
        'amount',
        'card_number',
        'payment_option',
        'raw_response'
    ];

    /**
     * Get the payfort's transaction.
     * @returns MorphOne
     */
    public function transaction()
    {
        return $this->morphOne(PaymentTransaction::class, 'methodable');
    }
}
