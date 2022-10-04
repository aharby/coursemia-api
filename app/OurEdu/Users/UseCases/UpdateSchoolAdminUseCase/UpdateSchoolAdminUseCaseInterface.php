<?php


namespace App\OurEdu\Users\UseCases\UpdateSchoolAdminUseCase;

use App\OurEdu\Users\Models\Student;

interface UpdateSchoolAdminUseCaseInterface
{
    public function updateSchoolAdmin(int $userId, array $data);
}
