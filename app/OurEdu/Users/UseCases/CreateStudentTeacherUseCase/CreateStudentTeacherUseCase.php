<?php

namespace App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase;


use App\OurEdu\Users\Models\StudentTeacher;
use App\OurEdu\Users\Repository\StudentTeacherRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase\CreateStudentTeacherUseCaseInterface;

class CreateStudentTeacherUseCase implements CreateStudentTeacherUseCaseInterface
{
    private $studentTeacherRepository;

    public function __construct(StudentTeacherRepositoryInterface $studentTeacherRepository)
    {
        $this->studentTeacherRepository = $studentTeacherRepository;
    }


    public function CreateStudentTeacher(array $data): ?StudentTeacher
    {
        return $this->studentTeacherRepository->create($data);
    }
}
