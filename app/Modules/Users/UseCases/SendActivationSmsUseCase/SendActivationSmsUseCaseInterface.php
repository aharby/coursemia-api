<?php

namespace App\Modules\Users\UseCases\SendActivationSmsUseCase;

use App\Modules\Users\User;

interface SendActivationSmsUseCaseInterface
{
    public function send(User $user);
}
