<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_device_id'
    ];
}
