<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\RegisterStudentTeacherUseCase;

use App\OurEdu\Users\Models\StudentTeacher;


interface RegisterStudentTeacherUseCaseInterface
{
    public function registerStudentTeacher(array $request, int $user_id): StudentTeacher;
}
