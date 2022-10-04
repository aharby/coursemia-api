<?php

namespace App\OurEdu\Users\UseCases\CreateStudentUseCase;


use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;

class CreateStudentUseCase implements CreateStudentUseCaseInterface
{
    private $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function createStudent(array $data): ?Student
    {
        return $this->studentRepository->create($data);
    }
}
