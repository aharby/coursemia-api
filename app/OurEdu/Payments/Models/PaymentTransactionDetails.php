<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;

class PaymentTransactionDetails extends BaseModel
{
    protected $fillable = [
        'payment_transaction_id',
        'subscribable_id',
        'subscribable_type'
    ];
    protected $with=['subscribable'];
    /**
     * Get the parent subscribable model.
     */
    public function subscribable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo()->withoutGlobalScopes()->withTrashed();
    }
}
