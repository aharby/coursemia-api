<?php

namespace App\OurEdu\Users\UseCases\CreateInstructorUseCase;


use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;

class CreateInstructorUseCase implements CreateInstructorUseCaseInterface
{
    private $repository;


    /**
     * CreateContentAuthorUseCase constructor.
     * @param InstructorRepositoryInterface $instructorRepository
     */
    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->repository = $instructorRepository;
    }


    public function CreateInstructor(array $data): ?Instructor
    {
        return $this->repository->create($data);
    }
}