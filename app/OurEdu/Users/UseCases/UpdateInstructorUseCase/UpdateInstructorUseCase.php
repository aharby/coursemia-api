<?php


namespace App\OurEdu\Users\UseCases\UpdateInstructorUseCase;


use App\OurEdu\Users\Repository\InstructorRepositoryInterface;

class UpdateInstructorUseCase implements UpdateInstructorUseCaseInterface
{
    private $repository;


    /**
     * UpdateInstructorUseCase constructor.
     * @param InstructorRepositoryInterface $instructorRepository
     */
    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->repository = $instructorRepository;
    }

    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateInstructorCase(int $userId, array $data): bool
    {
        $row = $this->repository->getInstructorByUserId($userId);
        return $this->repository->update($row, $data);
    }
}