<?php

namespace App\OurEdu\Contact;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends BaseModel {
    use SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'message',
    ];

}
