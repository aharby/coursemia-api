<?php

namespace App\OurEdu\Users\UseCases\SendActivationSmsUseCase;

use App\OurEdu\Users\User;

interface SendActivationSmsUseCaseInterface
{
    public function send(User $user);
}
