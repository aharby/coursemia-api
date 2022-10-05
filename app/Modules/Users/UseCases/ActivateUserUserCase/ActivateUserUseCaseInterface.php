<?php


namespace App\Modules\Users\UseCases\ActivateUserUserCase;


use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\User;

interface ActivateUserUseCaseInterface
{
    public function activate($token , UserRepositoryInterface $userRepository);


    /**
     * @param array $data
     * @param int $token
     * @param UserRepositoryInterface $userRepository
     * @return bool
     */
    public function activateWithSocialId(array $data,int $token, UserRepositoryInterface $userRepository):bool;

    public function activateWithOtp($code , UserRepositoryInterface $userRepository): ?User;

}
