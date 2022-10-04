<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\LoginUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface LoginUseCaseInterface
{
    public function login(array $request, UserRepositoryInterface $repository): array;
}
