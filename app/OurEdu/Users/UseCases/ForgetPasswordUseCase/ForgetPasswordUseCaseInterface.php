<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\ForgetPasswordUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface ForgetPasswordUseCaseInterface
{
    public function sendPasswordResetMail(
        string $email,
        UserRepositoryInterface $userRepository,
        bool $abilitiesUser = false
    ): array;

    public function updatePasswordUsingResetToken(string $token, array $data, UserRepositoryInterface $userRepository) : array;

    public function sendPasswordResetCode(
        $identifier,
        UserRepositoryInterface $userRepository,
        bool $abilitiesUser = false
    ): array;

    public function confirmPasswordResetCode($code, UserRepositoryInterface $userRepository);

}
