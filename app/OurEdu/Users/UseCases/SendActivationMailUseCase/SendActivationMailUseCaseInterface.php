<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\SendActivationMailUseCase;

interface SendActivationMailUseCaseInterface
{
    public function send($user,string $email = null);
}
