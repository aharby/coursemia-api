<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\ResetSchoolInstructorPasswordUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface ResetSchoolInstructorPasswordUseCaseInterface
{
    public function sendPasswordResetMail(string $email, UserRepositoryInterface $userRepository): array;

}
