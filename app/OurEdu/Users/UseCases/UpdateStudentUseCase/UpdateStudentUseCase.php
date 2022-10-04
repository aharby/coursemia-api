<?php


namespace App\OurEdu\Users\UseCases\UpdateStudentUseCase;



use App\OurEdu\Users\Repository\StudentRepositoryInterface;

class UpdateStudentUseCase implements UpdateStudentUseCaseInterface
{
    private $repository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->repository = $studentRepository;
    }

    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateStudentCase(int $userId, array $data): bool
    {
        $row = $this->repository->getStudentByUserId($userId);
        return $this->repository->update($row, $data);
    }
}
