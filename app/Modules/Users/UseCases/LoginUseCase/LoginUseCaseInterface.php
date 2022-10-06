<?php
declare(strict_types=1);

namespace App\Modules\Users\UseCases\LoginUseCase;

use App\Modules\Users\Repository\UserRepositoryInterface;

interface LoginUseCaseInterface
{
    public function login(array $request, UserRepositoryInterface $repository): array;
    public function profile(UserRepositoryInterface $repository): array;
}
