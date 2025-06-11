<?php

namespace App\Modules\ContactUs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsForm extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'name', 'email', 'country_code', 'phone', 'message',
];
}
