<?php

namespace App\OurEdu\Subscribes;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Payments\Models\Order;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends BaseModel
{
    use SoftDeletes, CreatedBy;

    protected $fillable =[
        'user_id',
        'payment_done',
        'subscripable_id',
        'subscripable_type',
        'order_id',
    ];

    public function subscripable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
