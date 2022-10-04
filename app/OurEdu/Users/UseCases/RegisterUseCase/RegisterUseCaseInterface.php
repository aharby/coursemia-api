<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\RegisterUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;

interface RegisterUseCaseInterface
{
    public function register(array $request, UserRepositoryInterface $repository): User;
}
