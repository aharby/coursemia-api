<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'merchant_reference',
        'customer_email',
        'customer_ip',
        'status',
        'methodable_id',
        'transaction_id',
        'methodable_type',          // Gateway Service Provider
        'payment_transaction_for',  // subject , course , vcr_spot and add_money_to_wallet
        'payment_method',            // visa vs wallet
        'payment_transaction_type',   // refund , deposit and withdrawal
        'parent_payment_transaction_id',
        'reviewed',                     // if reviewed by FailedTransactions or not
    ];


    public function getData()
    {
        return $this;
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->withTrashed();
    }

    /**
     * Get the parent methodable model (Payment Gateway).
     */
    public function methodable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function parentTransaction()
    {
        return $this->belongsTo(self::class, 'parent_payment_transaction_id');
    }

    public function scopeDeposit($query)
    {
        $query->where('payment_transaction_type', TransactionTypesEnums::DEPOSIT);
    }

    public function scopeWithdraw($query)
    {
        $query->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL);
    }

    public function detail()
    {
        return $this->hasOne(PaymentTransactionDetails::class);
    }
}
