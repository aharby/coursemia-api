<?php


namespace App\Modules\Users\UseCases\UpdateUserUseCase;


use App\Modules\Users\Repository\UserRepositoryInterface;

interface ActivateUserUseCaseInterface
{

    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateUser(UserRepositoryInterface $userRepository, array $data,int $id): bool;
}
