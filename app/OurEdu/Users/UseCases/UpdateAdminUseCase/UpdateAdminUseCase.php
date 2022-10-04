<?php


namespace App\OurEdu\Users\UseCases\UpdateAdminUseCase;


use App\OurEdu\Users\Repository\AdminRepositoryInterface;

class UpdateAdminUseCase implements UpdateAdminUseCaseInterface
{
    private $repository;


    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->repository = $adminRepository;
    }


    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateAdminUseCase(int $userId, array $data): bool
    {
        $row = $this->repository->getAdminByUserId($userId);
        return $this->repository->update($row, $data);    }
}
