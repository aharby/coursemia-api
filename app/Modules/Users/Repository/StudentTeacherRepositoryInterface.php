<?php


namespace App\Modules\Users\Repository;

use App\Modules\Users\Models\StudentTeacher;

interface StudentTeacherRepositoryInterface
{
    public function create(array $data): ?StudentTeacher;

    public function findOrFail(int $id): ?StudentTeacher;

    public function update(StudentTeacher $studentTeacher, array $data): bool;

    public function delete(StudentTeacher $studentTeacher): bool;

}
