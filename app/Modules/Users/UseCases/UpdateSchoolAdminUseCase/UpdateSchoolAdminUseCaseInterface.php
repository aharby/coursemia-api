<?php


namespace App\Modules\Users\UseCases\UpdateSchoolAdminUseCase;

use App\Modules\Users\Models\Student;

interface UpdateSchoolAdminUseCaseInterface
{
    public function updateSchoolAdmin(int $userId, array $data);
}
