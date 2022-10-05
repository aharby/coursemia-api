<?php
declare(strict_types=1);

namespace App\Modules\Users\UseCases\RegisterStudentUseCase;

use App\Modules\Users\Models\Student;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\User;

interface RegisterStudentUseCaseInterface
{
    public function registerStudent(array $request, int $user_id): Student;
}
