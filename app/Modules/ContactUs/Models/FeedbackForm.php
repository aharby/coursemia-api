<?php

namespace App\Modules\ContactUs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackForm extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'rating', 'comment',
];
}
