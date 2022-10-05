<?php

namespace App\Modules\Users\Models;

use App\Modules\BaseApp\BaseModel;
use App\Modules\Users\User;


class PasswordReset extends BaseModel
{

    protected $table = 'password_resets';

    public $timestamps = false;

    protected $fillable = ['email','token','created_at','mobile'];

}
