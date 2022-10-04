<?php


namespace App\OurEdu\Users\UseCases\CreateStudentUseCase;

use App\OurEdu\Users\Models\Student;

interface CreateStudentUseCaseInterface
{
    public function createStudent(array $data): ?Student;
}
