<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\RegisterStudentUseCase;

use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;

interface RegisterStudentUseCaseInterface
{
    public function registerStudent(array $request, int $user_id): Student;
}
