<?php


namespace App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase;

use App\OurEdu\Users\Models\StudentTeacher;

interface CreateStudentTeacherUseCaseInterface
{
    public function CreateStudentTeacher(array $data): ?StudentTeacher;
}
