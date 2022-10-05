<?php
declare(strict_types=1);

namespace App\Modules\Users\UseCases\RegisterUseCase;

use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\User;

interface RegisterUseCaseInterface
{
    public function register(array $request, UserRepositoryInterface $repository): User;
}
