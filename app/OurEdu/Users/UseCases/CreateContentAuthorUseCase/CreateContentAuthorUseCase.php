<?php


namespace App\OurEdu\Users\UseCases\CreateContentAuthorUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;

class CreateContentAuthorUseCase implements CreateContentAuthorUseCaseInterface
{
    private $repository;

    /**
     * CreateContentAuthorUseCase constructor.
     * @param ContentAuthorRepositoryInterface $contentAuthorRepository
     */
    public function __construct(ContentAuthorRepositoryInterface $contentAuthorRepository)
    {
        $this->repository = $contentAuthorRepository;
    }

    /**
     * @param array $data
     * @return ContentAuthor|null
     */
    public function CreateContentAuthor(array $data): ?ContentAuthor
    {
        return $this->repository->create($data);
    }
}