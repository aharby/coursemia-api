<?php


namespace App\OurEdu\Users\Repository;

use App\OurEdu\Users\Models\StudentTeacher;

interface StudentTeacherRepositoryInterface
{
    public function create(array $data): ?StudentTeacher;

    public function findOrFail(int $id): ?StudentTeacher;

    public function update(StudentTeacher $studentTeacher, array $data): bool;

    public function delete(StudentTeacher $studentTeacher): bool;

}
