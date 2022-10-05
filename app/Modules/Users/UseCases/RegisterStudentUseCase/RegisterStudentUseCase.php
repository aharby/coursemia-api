<?php

declare(strict_types=1);

namespace App\Modules\Users\UseCases\RegisterStudentUseCase;

use App\Modules\Subjects\Models\Subject;
use App\Modules\Users\Models\Student;
use App\Modules\Users\Repository\StudentRepositoryInterface;

class RegisterStudentUseCase implements RegisterStudentUseCaseInterface
{
    private $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function registerStudent(array $request, int $user_id): Student
    {
        $student = $this->studentRepository->create([
            'user_id' => $user_id,
            'educational_system_id' => $request['educational_system_id'],
            'class_id' => $request['class_id'],
            'school_id' => $request['school_id'],
            'academical_year_id' => $request['academical_year_id'],
            'birth_date' => $request['birth_date'],
        ]);

        //  subscribe any new student to aptitude subject
        $aptitudeSubject = Subject::where('is_aptitude',true)->first();
        if($aptitudeSubject)
        {
            $student->subjects()->attach($aptitudeSubject->id);

        }

        return $student;
    }
}
