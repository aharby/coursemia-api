<?php

namespace App\OurEdu\Users\UseCases\UpdateStudentUseCase;

interface UpdateStudentUseCaseInterface
{
    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateStudentCase(int $userId, array $data): bool;
}
