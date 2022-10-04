<?php

namespace App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase;

interface UpdateContentAuthorUseCaseInterface
{
    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateContentUseCase(int $userId, array $data): bool;
}