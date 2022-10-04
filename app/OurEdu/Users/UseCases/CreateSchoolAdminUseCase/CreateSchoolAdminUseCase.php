<?php

namespace App\OurEdu\Users\UseCases\CreateSchoolAdminUseCase;

use App\OurEdu\Users\Repository\SchoolAdminRepositoryInterface;

class CreateSchoolAdminUseCase implements CreateSchoolAdminUseCaseInterface
{
    private $repository;

    public function __construct(SchoolAdminRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createSchoolAdmin(array $data)
    {
       $this->repository->create($data);
    }
}
