<?php


namespace App\Modules\Users\UseCases\CreateUserUseCase;


use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\User;

interface CreateUserUseCaseInterface
{

    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @return User|null
     */
    public function createUser(UserRepositoryInterface $userRepository, array $data): ?User;
}
