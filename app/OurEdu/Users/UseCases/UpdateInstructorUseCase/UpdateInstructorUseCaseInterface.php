<?php

namespace App\OurEdu\Users\UseCases\UpdateInstructorUseCase;

interface UpdateInstructorUseCaseInterface
{
    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateInstructorCase(int $userId, array $data): bool;
}