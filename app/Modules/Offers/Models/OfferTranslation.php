<?php


namespace App\Modules\Offers\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
    ];
}
