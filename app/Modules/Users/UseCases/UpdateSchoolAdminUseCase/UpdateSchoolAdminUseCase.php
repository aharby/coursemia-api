<?php

namespace App\Modules\Users\UseCases\UpdateSchoolAdminUseCase;

use App\Modules\Users\Repository\SchoolAdminRepositoryInterface;

class UpdateSchoolAdminUseCase implements UpdateSchoolAdminUseCaseInterface
{
    private $repository;

    public function __construct(SchoolAdminRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function updateSchoolAdmin(int $userId, array $data)
    {
       $this->repository->update($userId, $data);
    }
}
