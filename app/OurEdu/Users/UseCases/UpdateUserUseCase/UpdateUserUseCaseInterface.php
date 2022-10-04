<?php


namespace App\OurEdu\Users\UseCases\UpdateUserUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface UpdateUserUseCaseInterface
{

    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateUser(UserRepositoryInterface $userRepository, array $data,int $id): bool;
}