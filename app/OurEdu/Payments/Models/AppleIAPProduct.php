<?php

namespace App\OurEdu\Payments\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppleIAPProduct extends BaseModel
{
    protected $table = "apple_iap_products";
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_id',
        'title',
        'description',
        'price',
        'currency',
    ];

    /**
     * Modify column.
     *
     * @param  string  $value
     * @return string
     */
    public function getProductIdAttribute($value)
    {
        return (string)($value < 10 ? "0$value" : $value);
    }
}
