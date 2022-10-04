<?php


namespace App\OurEdu\Users\UseCases\CreateUserUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;

interface CreateUserUseCaseInterface
{

    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @return User|null
     */
    public function createUser(UserRepositoryInterface $userRepository, array $data): ?User;
}