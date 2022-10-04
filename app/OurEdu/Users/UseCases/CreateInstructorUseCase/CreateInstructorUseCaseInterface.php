<?php


namespace App\OurEdu\Users\UseCases\CreateInstructorUseCase;

use App\OurEdu\Users\Models\Instructor;

interface CreateInstructorUseCaseInterface
{
    public function CreateInstructor(array $data): ?Instructor;
}