<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppleIAPReceipt extends BaseModel
{
    protected $table = "apple_iap_receipt";

    protected $fillable = [
        'bundle_id',
        'transaction_id',
        'product_id',
        'raw_data',
        'verified_at',
    ];
}
