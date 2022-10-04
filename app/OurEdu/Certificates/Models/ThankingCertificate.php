<?php

namespace App\OurEdu\Certificates\Models;

use App\OurEdu\BaseApp\Traits\HasAttach;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThankingCertificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "attributes",
        "image"
    ];

    public function getAttributesAttribute()
    {
        return json_decode($this->attributes['attributes'],true);
    }
}
