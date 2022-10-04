<?php

namespace App\OurEdu\Users\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;


class PasswordReset extends BaseModel
{

    protected $table = 'password_resets';

    public $timestamps = false;

    protected $fillable = ['email','token','created_at','mobile'];

}
