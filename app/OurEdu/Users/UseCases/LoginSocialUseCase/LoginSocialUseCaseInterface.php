<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\LoginSocialUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface LoginSocialUseCaseInterface
{
    public function loginOrRegisterByFacebook($data): array;
    public function twitterAuthentication($data);
}
