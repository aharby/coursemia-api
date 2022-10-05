<?php


namespace App\Modules\Users\UseCases\ActivateUserUserCase;


use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\User;

class ActivateUserUseCase implements ActivateUserUseCaseInterface
{
    public function activate($token, UserRepositoryInterface $userRepository)
    {
        $user = $userRepository->findUserByConfirmToken($token);

        if ($user) {
            $user->confirm_token = null;
            $user->confirmed = true;
            $user->save();
        }

        return $user;
    }

    /**
     * @param array $data
     * @param int $token
     * @param UserRepositoryInterface $userRepository
     * @return bool
     */
    public function activateWithSocialId(array $data, int $token, UserRepositoryInterface $userRepository): bool
    {
        $user = $userRepository->findUserByConfirmToken($token);
        if ($user) {
            return $userRepository->update($user, [
                'password' => $data['password'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'confirm_token' => null,
                'confirmed' => true
            ]);
        }
        return false;
    }

    public function activateWithOtp($code, UserRepositoryInterface $userRepository): ?User
    {
        $user = $userRepository->findUserByOtp($code);
        if ($user) {
            $user->otp = null;
            $user->confirmed = true;
            $user->save();
        }

        return $user;
    }
}
