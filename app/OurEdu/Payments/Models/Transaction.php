<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends BaseModel
{
    use SoftDeletes, CreatedBy;

    protected $fillable = [
        'user_id',
        'subscribable_id',
        'subscribable_type',
        'amount',
        'transaction_type'
    ];


    public function getData()
    {
        return $this;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribable()
    {
        return $this->morphTo();
    }
}
