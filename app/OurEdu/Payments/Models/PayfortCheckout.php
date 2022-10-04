<?php

namespace App\OurEdu\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PayfortCheckout extends Model
{
    protected $fillable = [
        'token_name',
        'response_code',
        'response_message',
        'amount',
        'currency',
        'card_number',
        'card_holder_name',
        'payment_option',
        'expiry_date',
        'fort_id',
        'customer_email'
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
