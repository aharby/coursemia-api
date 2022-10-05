<?php

namespace App\Modules\Users\UseCases\SendLoginOtp;

use App\Modules\Users\User;

interface SendLoginOtp
{
    public function send(User $user);
}
