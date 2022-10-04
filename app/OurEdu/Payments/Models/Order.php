<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'order_key',
        'payment_method',
        'amount',
        'user_id',
    ];


    public function getData()
    {
        return $this;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
