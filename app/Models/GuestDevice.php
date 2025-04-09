<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_device_id'
    ];

    public function cartCourses()
    {
        return $this->hasMany(\App\Modules\Payment\Models\CartCourse::class);
    }
}
