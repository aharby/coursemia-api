<?php


namespace App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase;


use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;

class UpdateContentAuthorUseCase implements UpdateContentAuthorUseCaseInterface
{
    private $repository;

    /**
     * UpdateContentAuthorUseCase constructor.
     * @param ContentAuthorRepositoryInterface $contentAuthorRepository
     */
    public function __construct(ContentAuthorRepositoryInterface $contentAuthorRepository)
    {
        $this->repository = $contentAuthorRepository;
    }

    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function UpdateContentUseCase(int $userId, array $data): bool
    {
        $row = $this->repository->getContentAuthorByUserId($userId);
        return $this->repository->update($row, $data);
    }
}