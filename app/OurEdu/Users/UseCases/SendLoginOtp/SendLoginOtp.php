<?php

namespace App\OurEdu\Users\UseCases\SendLoginOtp;

use App\OurEdu\Users\User;

interface SendLoginOtp
{
    public function send(User $user);
}
