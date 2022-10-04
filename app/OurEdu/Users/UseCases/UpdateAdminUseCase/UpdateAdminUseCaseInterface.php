<?php

namespace App\OurEdu\Users\UseCases\UpdateAdminUseCase;

interface UpdateAdminUseCaseInterface
{
    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateAdminUseCase(int $userId, array $data): bool;
}
