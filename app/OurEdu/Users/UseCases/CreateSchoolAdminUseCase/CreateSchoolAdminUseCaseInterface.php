<?php


namespace App\OurEdu\Users\UseCases\CreateSchoolAdminUseCase;

use App\OurEdu\Users\Models\Student;

interface CreateSchoolAdminUseCaseInterface
{
    public function createSchoolAdmin(array $data);
}
