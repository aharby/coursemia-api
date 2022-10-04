<?php

declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\RegisterStudentTeacherUseCase;

use App\OurEdu\Users\Models\StudentTeacher;
use App\OurEdu\Users\Repository\StudentTeacherRepositoryInterface;


class RegisterStudentTeacherUseCase implements RegisterStudentTeacherUseCaseInterface
{
    private $studentTeacherRepository;

    public function __construct(StudentTeacherRepositoryInterface $studentTeacherRepository)
    {
        $this->studentTeacherRepository = $studentTeacherRepository;
    }

    public function registerStudentTeacher(array $request, int $user_id): StudentTeacher
    {
        $studentTeacher = $this->studentTeacherRepository->create([
            'user_id' => $user_id
        ]);

        return $studentTeacher;
    }
}
